<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Observation;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ObservationController extends Controller
{
    protected function create(Request $request)
    {        
        try {
            $obs_title = isset($request->obs_title) ? ucfirst(trim($request->obs_title)) : null;
            $obs_description = isset($request->obs_description) ? ucfirst(trim($request->obs_description)) : null;
            $obs_derivative = isset($request->obs_derivative) ? ucfirst(trim($request->obs_derivative)) : null;
            $obs_state = isset($request->obs_state) ? $request->obs_state : 'P';
            $obs_tra_id = isset($request->obs_tra_id) ? $request->obs_tra_id : null;

            DB::beginTransaction();
            $observation = Observation::create([
                'obs_title' => $obs_title,
                'obs_description' => $obs_description,
                'obs_derivative' => $obs_derivative,
                'obs_state' => $obs_state,
                'obs_tra_id' => $obs_tra_id,
            ]);

            DB::commit();
            $content = response()->json(['state' => 'success', 'data' => $observation], 201);
            return $content;
        } catch (QueryException $e) {
            DB::rollback();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            error_log($errorMessage);
            echo json_encode(['error' => $errorMessage]);
            if ($errorCode == 23000) {
                return response()->json(['state' => 'error', 'message' => 'Ya existe una observacion con este numero.'], 422);
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
                'obs_title' => 'required|string|max:50',
                'obs_description' => 'required|string|max:255'
            ]);

            $observation = Observation::findOrFail($request->obs_id);
            $observation->update([
                'obs_title' => ucwords(strtolower(trim($request->obs_title))),
                'obs_description' => ucwords(strtolower(trim($request->obs_description))),
            ]);
            $updatedData = Observation::find($observation->obs_id);

            return response()->json(['state' => 'success', 'data' => $updatedData, 'message' => 'Actualización exitosa'], 200);
        } catch (ValidationException $e) {
            return response()->json(['state' => 'error', 'message' => 'Error de validación', 'details' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Recurso no encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error interno del servidor', 'details' => $e->getMessage()], 500);
        }
    }

}
