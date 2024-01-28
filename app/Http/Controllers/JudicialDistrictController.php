<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JudicialDistrict;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class JudicialDistrictController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $judicialDistricts = JudicialDistrict::latest()->get();
            return response()->json(['state' => 'success', 'data' => $judicialDistricts], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function show(Request $request)
    {
        try {
            $judicialDistrict = JudicialDistrict::findOrFail($request->judis_id);

            return response()->json(['state' => 'success', 'data' => $judicialDistrict], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['state' => 'error', 'message' => 'Recurso no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error inesperado en JudicialDistrictController@show: ' . $e->getMessage());
            return response()->json(['state' => 'error', 'message' => 'Error inesperado. Por favor, contacta al soporte técnico.'], 500);
        }
    }

    protected function store(Request $request)
    {
        $this->validate($request, [
            'judis_nombre' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $judicialDistrict = JudicialDistrict::create([
                'judis_nombre' => ucwords(strtolower(trim($request->judis_nombre))),
            ]);
            
            DB::commit();
            return response()->json(['state' => 'success', 'data' => $judicialDistrict], 201);
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
                'judis_nombre' => 'required|string|max:255',
            ]);

            $judicialDistrict = JudicialDistrict::findOrFail($request->judis_id);
            $judicialDistrict->update([
                'judis_nombre' => ucwords(strtolower(trim($request->judis_nombre))),
            ]);
            $updatedData = JudicialDistrict::find($judicialDistrict->judis_id);

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

            $judicialDistrict = JudicialDistrict::findOrFail($request->judis_id);
            $judicialDistrict->delete();

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
