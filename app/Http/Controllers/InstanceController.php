<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InstanceController extends Controller
{

    public function index()
    {
        try {
            $instances = Instance::latest()->get();
            return response()->json(['state' => 'success', 'data' => $instances], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function show(Request $request){
        try{
            $instances = Instance::findOrFail($request->ins_id);
            return response()->json(['state' => 'success', 'data' => $instances], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['state' => 'error', 'message' => 'Recurso no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error inesperado en JudicialDistrictController@show: ' . $e->getMessage());
            return response()->json(['state' => 'error', 'message' => 'Error inesperado. Por favor, contacta al soporte técnico.'], 500);
        }
    }

    protected function store(Request $request)
    {
        $this->validate($request, [
            'ins_nombre' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $instances = Instance::create([
                'ins_nombre' => ucwords(strtolower(trim($request->ins_nombre))),
            ]);
            
            DB::commit();
            return response()->json(['state' => 'success', 'data' => $instances], 201);
        } catch (QueryException $e) {
            DB::rollback();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();

            if ($errorCode == 23000) {
                return response()->json(['state' => 'error', 'message' => 'Ya existe un registro con este nombre.'], 422);
            }

            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function update(Request $request)
    {
        try {
            $this->validate($request, [
                'ins_nombre' => 'required|string|max:255',
            ]);

            $instances = Instance::findOrFail($request->ins_id);
            $instances->update([
                'ins_nombre' => ucwords(strtolower(trim($request->ins_nombre))),
            ]);
            $updatedData = Instance::find($instances->ins_id);

            return response()->json(['state' => 'success', 'data' => $updatedData, 'message' => 'Actualización exitosa'], 200);
        } catch (ValidationException $e) {
            return response()->json(['state' => 'error', 'message' => 'Error de validación', 'details' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Recurso no encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error interno del servidor', 'details' => $e->getMessage()], 500);
        }
    }

    protected function destroy(Request $request)
    {
        try {
            DB::beginTransaction();

            $instances = Instance::findOrFail($request->ins_id);
            $instances->delete();

            DB::commit();

            return response()->json(['state' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Recurso no encontrado'], 404);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['state' => 'error', 'message' => 'Error interno del servidor', 'details' => $e->getMessage()], 500);
        }
    }
}
