<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Trade;
use App\Models\TradeReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TradeReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function create(Request $request)
    {        
        try {
            $rep_oficio = isset($request->rep_oficio) ? strtoupper(trim($request->rep_oficio)) : null;
            $rep_informe = isset($request->rep_informe) ? strtoupper(trim($request->rep_informe)) : null;
            $rep_tra_id = isset($request->rep_tra_id) ? $request->rep_tra_id : null;
            $rep_are_id = isset($request->rep_are_id) ? $request->rep_are_id : null;
            $rep_anio = $request->rep_anio;
            DB::beginTransaction();
            $report = TradeReport::create([
                'rep_oficio' => $rep_oficio,
                'rep_informe' => $rep_informe,
                'rep_tra_id' => $rep_tra_id,
                'rep_are_id' => $rep_are_id,
                'rep_anio' => $rep_anio
            ]);

            // Encontrar trade
            $trade = Trade::findOrFail($rep_tra_id);
            // Actualizar campos
            $trade->tra_state_law = 'F';

             // Guarda los cambios en la base de datos
            $trade->save();
            
            DB::commit();
            return response()->json(['state' => 'success', 'data' => $report], 201);
        } catch (QueryException $e) {
            DB::rollback();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            error_log($errorMessage);
            echo json_encode(['error' => $errorMessage]);
            if ($errorCode == 23000) {
                return response()->json(['state' => 'error', 'message' => 'Ya existe un oficio con este numero.'], 422);
            }

            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function getNumInfoNumber(Request $request)
    {
        try{
            $abo_id = $request->abo_id;
            $currentYear = Carbon::now()->year;

            // Filtrar TradeReport donde el año es el actual y el abo_id en la tabla Trade es el especificado
            $maxTraNumber = TradeReport::where('rep_anio', $currentYear)
            ->with('trade', function ($query) use ($abo_id) {
                $query->where('tra_abo_id', $abo_id); // Aquí es donde debes asegurarte de filtrar por abo_id
            })
            ->get();

            return response()->json([
                'state' => 'success',
                'maxTraNumber' => $maxTraNumber,
                'abo_id' => $abo_id
            ]);


        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function getNextRepNumber()
    {
        try {
            $currentYear = Carbon::now()->year;

            // Obtener el máximo tra_number del año actual
            $maxTraNumber = TradeReport::where('rep_anio', $currentYear)->max('rep_oficio');

            // Si no hay registros del año actual, inicializar el número de secuencia
            if (!$maxTraNumber) {
                $nextRepNumber = '0001-' . $currentYear . '-UNPRG-OAJ';
                return response()->json(['state' => 'success', 'nextRepNumber' => $nextRepNumber], 200);
            }

            // Extraer el número y el año del máximo tra_number
            $parts = explode('-', $maxTraNumber);
            $currentNumber = intval($parts[0]);
            $year = $parts[1];

            // Incrementar el número en uno
            $nextNumber = $currentNumber + 1;

            // Formatear el nuevo número
            $nextRepNumber = sprintf("%04d", $nextNumber) . '-' . $year . '-UNPRG-OAJ';

            return response()->json(['state' => 'success', 'nextRepNumber' => $nextRepNumber], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }
}
