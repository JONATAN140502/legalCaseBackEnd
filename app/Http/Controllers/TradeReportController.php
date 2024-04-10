<?php

namespace App\Http\Controllers;

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

            DB::beginTransaction();
            $report = TradeReport::create([
                'rep_oficio' => $rep_oficio,
                'rep_informe' => $rep_informe,
                'rep_tra_id' => $rep_tra_id,
                'rep_are_id' => $rep_are_id
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
}
