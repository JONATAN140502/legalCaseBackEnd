<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index(){
        $assistants = Assistant::orderBy('created_at', 'DESC')->with('persona')->get();
        
        $data = $assistants->map(function ($assistant) {
            return [
                'nat_correo' => $assistant->persona->nat_correo,
                'ass_id' => $assistant->ass_id,
                'ass_carga_laboral' => $assistant->ass_carga_laboral,
                'ass_disponibilidad' => $assistant->ass_disponibilidad,
                'per_id' => $assistant->persona->per_id,
                'nat_dni' => $assistant->persona->nat_dni,
                'nat_apellido_paterno' => ucwords(strtolower($assistant->persona->nat_apellido_paterno)),
                'nat_apellido_materno' => ucwords(strtolower($assistant->persona->nat_apellido_materno)),
                'nat_nombres' => ucwords(strtolower($assistant->persona->nat_nombres)),
                'nat_telefono' => $assistant->persona->nat_telefono,
            ];
        });

        return \response()->json(['data' => $data], 200);
        
    }

    public function listTrades(Request $request){
        try{
            $assistant = Assistant::findOrFail($request->ass_id);
            $trades = $assistant->trades()->with('area')->get();
            return response()->json(['state' => 'success', 'data' => $trades], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }
}
