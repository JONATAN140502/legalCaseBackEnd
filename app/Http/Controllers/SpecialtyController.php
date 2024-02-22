<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class SpecialtyController extends Controller
{
    public function index(Request $request)
    {
        // $specialties = \App\Models\Specialty::where('ins_id', $request->ins_id)
        //     ->orderBy('created_at', 'DESC')
        //     ->get(['esp_id','esp_nombre','ins_id']);
        $specialties = \App\Models\Specialty::orderBy('created_at', 'DESC')->get(['esp_id','esp_nombre']);

        return response()->json(['data' => $specialties], 200);
    }
    
    protected function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $esp= \App\Models\Specialty::create([
                'esp_nombre' =>  strtoupper(trim($request->esp_nombre)),
                // 'ins_id' =>  strtoupper(trim($request->ins_id)),
            ]);
            \App\Models\Audit::create([
                'accion'=>'Crear de Especialidad',
               'model'=>'\App\Models\Specialty',
                'model_id'=>$esp->esp_id,
                'user_id'=>\Auth::user()->id,
            ]);
            DB::commit();
            return response()->json(['state' => 0, 'data' => $esp], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }

    protected function update(Request $request){
        try{
            DB::beginTransaction();
            $esp = \App\Models\Specialty::find($request->esp_id);
            $esp->esp_nombre = strtoupper(trim($request->esp_nombre));
            // $esp->ins_id = strtoupper(trim($request->ins_id));
            $esp->save();
            \App\Models\Audit::create([
                'accion'=>'Edición de Especialidad',
               'model'=>'\App\Models\Specialty',
                'model_id'=>$esp->esp_id,
                'user_id'=>\Auth::user()->id,
            ]);
            DB::commit();
            return \response()->json(['state' => 0], 200);

        }catch(Exception $e){
            DB::rollBack();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function destroy(Request $request){
        try {
            DB::beginTransaction();
            $esp= \App\Models\Specialty::find($request->esp_id);
            $esp->delete();
            \App\Models\Audit::create([
                'accion'=>'Eliminacion de Especialidad',
               'model'=>'\App\Models\Specialty',
                'model_id'=>$esp->esp_id,
                'user_id'=>\Auth::user()->id,
            ]);
            DB::commit();
            return \response()->json(['state' => 0, 200]);

        } catch (Exception $e) {
            DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }

    }
}

