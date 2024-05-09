<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $trades = Trade::with([
                'area',
                'lawyer.persona',
                'report'

            ])->orderBy('created_at', 'asc')->get();

            return response()->json(['state' => 'success', 'data' => $trades], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function create(Request $request)
    {        
        try {
            $tra_number = isset($request->tra_number) ? strtoupper(trim($request->tra_number)) : null;
            $tra_name = isset($request->tra_name) ? ucfirst(trim($request->tra_name)) : null;
            $tra_doc_recep = isset($request->tra_doc_recep) ? strtoupper(trim($request->tra_doc_recep)) : null;
            $tra_exp_ext = isset($request->tra_exp_ext) ? strtoupper(trim($request->tra_exp_ext)) : null;
            $tra_matter = isset($request->tra_matter) ? ucfirst(trim($request->tra_matter)) : null;
            $tra_arrival_date = isset($request->tra_arrival_date) ? $request->tra_arrival_date : null;
            $tra_state_law = isset($request->tra_state_law) ? ucfirst($request->tra_state_law) : null;
            $tra_ubication = isset($request->tra_ubication) ? strtoupper(trim($request->tra_ubication)) : null;
            $tra_are_id = isset($request->tra_are_id) ? $request->tra_are_id : null;
            $tra_abo_id = isset($request->tra_abo_id)? $request->tra_abo_id : null;
            $tra_der_date = isset($request->tra_abo_id)? $tra_arrival_date : null;
            $tra_pdf = isset($request->tra_pdf) ? $request->tra_pdf : null;
            $anio = $request->anio;
            
            DB::beginTransaction();
            $trade = Trade::create([
                'tra_number' => $tra_number,
                'tra_name' => $tra_name,
                'tra_exp_ext' => $tra_exp_ext,
                'tra_doc_recep' => $tra_doc_recep,
                'tra_matter' => $tra_matter,
                'tra_arrival_date' => $tra_arrival_date,
                'tra_state_law' => $tra_state_law,
                'tra_ubication' => $tra_ubication,
                'tra_der_date' => $tra_der_date,
                'tra_are_id' => $tra_are_id,
                'tra_abo_id' => $tra_abo_id,
                'tra_pdf' => $tra_pdf,
                'anio' => $anio,
            ]);
            
            DB::commit();
            return response()->json(['state' => 'success', 'data' => $trade], 201);
        } catch (QueryException $e) {
            DB::rollback();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            error_log($errorMessage);
            echo json_encode(['error' => $errorMessage]);
            if ($errorCode == 23000) {
                return response()->json(['state' => 'error', 'message' => 'Ya existe un expediente con este numero.'], 422);
            }

            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function derivar(Request $request)
    {
        try {
            $tra_id = $request->tra_id;
            $tra_abo_id = isset($request->tra_abogado)? $request->tra_abogado : null;
            $tra_der_date = isset($request->tra_der_date)? $request->tra_der_date : null;
            $trade = Trade::findOrFail($request->tra_id);
            $trade->update([
                'tra_id' => $tra_id,
                'tra_abo_id' => $tra_abo_id,
                'tra_der_date' => $tra_der_date
            ]);
            $updatedData = trade::find($trade->tra_id);
            return response()->json(['state' => 'success', 'data' => $updatedData, 'message' => 'Actualización exitosa'], 200);
        } catch (QueryException $e) {
            DB::rollback();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            error_log($errorMessage);
            echo json_encode(['error' => $errorMessage]);
            if ($errorCode == 23000) {
                return response()->json(['state' => 'error', 'message' => 'Ya existe un expediente con este numero.'], 422);
            }

            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function show($id)
    {
        try {
            $trade = Trade::with([
                'area','lawyer.persona','report.area'=> function ($query) {
                    // Ordenar las observaciones por 'created_at' de forma descendente
                    $query->orderBy('created_at', 'desc');
                }
                ])->find($id);
            return response()->json(['state' => 'success', 'trade' => $trade], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }

    }

    protected function update(Request $request)
    {
        try {
            $trade = Trade::findOrFail($request->tra_id);
            $trade->update([
                'tra_name' => ucfirst(trim($request->tra_name)),
                'tra_matter'=> ucfirst($request->tra_matter),
                'tra_exp_ext' => isset($request->tra_exp_ext) ? strtoupper(trim($request->tra_exp_ext)) : null,
                'tra_doc_recep' => isset($request->tra_doc_recep) ? strtoupper(trim($request->tra_doc_recep)) : null,
                'tra_arrival_date' => isset($request->tra_arrival_date) ? $request->tra_arrival_date : null,
                'tra_ubication' => isset($request->tra_ubication) ? strtoupper(trim($request->tra_ubication)) : null,
                'tra_are_id' => $request->tra_are_id,
                'tra_abo_id' => $request->tra_abo_id
            ]);
            $updatedData = trade::find($trade->tra_id);

            return response()->json(['state' => 'success', 'data' => $updatedData, 'message' => 'Actualización exitosa'], 200);
        } catch (ValidationException $e) {
            return response()->json(['state' => 'error', 'message' => 'Error de validación', 'details' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Recurso no encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error interno del servidor', 'details' => $e->getMessage()], 500);
        }
    }

    protected function getNextTraNumber()
    {
        try {
            // Obtener el año actual
            $currentYear = Carbon::now()->year;

            // Obtener el máximo tra_number del año actual
            $maxTraNumber = Trade::where('anio', $currentYear)->max('tra_number');

             // Si no hay registros del año actual, inicializar el número de secuencia
            if (!$maxTraNumber) {
                $nextTraNumber = '0001-' . $currentYear . '-OAJ';
                return response()->json(['state' => 'success', 'nextTraNumber' => $nextTraNumber], 200);
            }


            // Extraer el número y el año del máximo tra_number
            $parts = explode('-', $maxTraNumber);
            $currentNumber = intval($parts[0]);
            $year = $parts[1];

            // Incrementar el número en uno
            $nextNumber = $currentNumber + 1;

            // Formatear el nuevo número
            $nextTraNumber = sprintf("%04d", $nextNumber) . '-' . $year . '-OAJ';

            return response()->json(['state' => 'success', 'nextTraNumber' => $nextTraNumber], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

}
