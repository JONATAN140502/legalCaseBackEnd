<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lawyer;
use App\Models\Trade;
use App\Models\Proceeding;
use App\Models\TradeReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TradeReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $reports = TradeReport::with([
                'trade.lawyer.persona', 'area' ,'proceeding'

            ])->orderBy('created_at', 'asc')->get();

            return response()->json(['state' => 'success', 'data' => $reports], 200);
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
            $rep_oficio = isset($request->rep_oficio) ? strtoupper(trim($request->rep_oficio)) : null;
            $rep_informe = isset($request->rep_informe) ? strtoupper(trim($request->rep_informe)) : null;
            if($rep_informe !== null && $request->abo_id === 4){
                // Dividimos la cadena en segmentos basados en el guion '-'
                $segments = explode("-", $rep_informe);
                $part1 = implode("-", array_slice($segments, 0, 4));
                $part2 = implode("-", array_slice($segments, 4));
                $rep_informe = $part1;
                $rep_ext_informe = $part2;
            }else{
                $rep_ext_informe = null;
            }
            $rep_tra_id = isset($request->rep_tra_id) ? $request->rep_tra_id : null;
            $rep_are_id = isset($request->rep_are_id) ? $request->rep_are_id : null;
            $rep_anio = $request->rep_anio;
            $rep_exp_id = null;
            $rep_matter = null;
            $rep_arrival_date = isset($request->rep_arrival_date)? $request->rep_arrival_date : null;
            DB::beginTransaction();
            $report = TradeReport::create([
                'rep_oficio' => $rep_oficio,
                'rep_informe' => $rep_informe,
                'rep_tra_id' => $rep_tra_id,
                'rep_are_id' => $rep_are_id,
                'rep_anio' => $rep_anio,
                'rep_exp_id' => $rep_exp_id,
                'rep_matter' => $rep_matter,
                'rep_arrival_date' => $rep_arrival_date,
                'rep_ext_informe' =>$rep_ext_informe
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

    protected function createExpLeg(Request $request)
    {
        try {
            $rep_oficio = isset($request->rep_oficio) ? strtoupper(trim($request->rep_oficio)) : null;
            $rep_informe = isset($request->rep_informe) ? strtoupper(trim($request->rep_informe)) : null;
            if($rep_informe !== null && $request->rep_abo_id === 4){
                // Dividimos la cadena en segmentos basados en el guion '-'
                $segments = explode("-", $rep_informe);
                $part1 = implode("-", array_slice($segments, 0, 4));
                $part2 = implode("-", array_slice($segments, 4));
                $rep_informe = $part1;
                $rep_ext_informe = $part2;
            }else{
                $rep_ext_informe = null;
            }
            $rep_tra_id = null;
            $rep_are_id = isset($request->rep_are_id) ? $request->rep_are_id : null;
            $rep_anio = $request->rep_anio;
            $rep_exp_id = isset($request->rep_exp_id) ? $request->rep_exp_id : null;
            $rep_matter = isset($request->rep_matter) ? $request->rep_matter : null;
            $rep_arrival_date = isset($request->rep_arrival_date)? $request->rep_arrival_date : null;
            $rep_abo_id = isset($request->rep_abo_id)? $request->rep_abo_id : null;
            DB::beginTransaction();
            $report = TradeReport::create([
                'rep_oficio' => $rep_oficio,
                'rep_informe' => $rep_informe,
                'rep_tra_id' => $rep_tra_id,
                'rep_are_id' => $rep_are_id,
                'rep_anio' => $rep_anio,
                'rep_exp_id' => $rep_exp_id,
                'rep_matter' => $rep_matter,
                'rep_arrival_date' => $rep_arrival_date,
                'rep_ext_informe' =>$rep_ext_informe,
                'rep_abo_id' => $rep_abo_id
            ]);
            
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

    protected function update(Request $request){
        try {
            $report = TradeReport::findOrFail($request->rep_id);
            $report->update([
                'rep_matter'=> ucfirst($request->rep_matter),
                'rep_arrival_date' => isset($request->rep_arrival_date) ? $request->rep_arrival_date : null,
                'rep_are_id' => $request->rep_are_id
            ]);
            $updatedData = trade::find($report->rep_id);

            return response()->json(['state' => 'success', 'data' => $updatedData, 'message' => 'Actualización exitosa'], 200);
        } catch (ValidationException $e) {
            return response()->json(['state' => 'error', 'message' => 'Error de validación', 'details' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Recurso no encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error interno del servidor', 'details' => $e->getMessage()], 500);
        }
    }

    protected function getNumInfoNumber($abo)
    {
        try{
            $abo_id = (int) $abo;
            $anio = Carbon::now()->year;
            if($abo_id === 4 || $abo_id === 17 || $abo_id === 18){
                $tra_abo_ids = [4, 17, 18];
                $trades = Trade::with('report')
                ->whereIn('tra_abo_id', $tra_abo_ids)
                ->where('anio', $anio)
                ->whereHas('report')
                ->get();

                $reports_exp = TradeReport::with('proceeding')
                ->whereIn('rep_abo_id', $tra_abo_ids)
                ->where('rep_anio', $anio)
                ->whereHas('proceeding')
                ->get();
            }else{
                $abogado = Lawyer::find($abo_id);
                $trades = Trade::with('report')
                ->where('tra_abo_id', $abo_id)
                ->where('anio', $anio)
                ->whereHas('report')
                ->get();

                $reports_exp = TradeReport::with('proceeding')
                ->where('rep_abo_id', $abo_id)
                ->where('rep_anio', $anio)
                ->whereHas('proceeding')
                ->get();
            }

            $reports = $trades->map(function ($trade) {
                return $trade->report;
            })->filter();

            $reports = $reports->merge($reports_exp);

            $maxReport = $reports->max('rep_informe');
            $parteEspecifica = substr($maxReport, strpos($maxReport, '-'));

            //Otalle, Palomino, Milagros
            if($abo_id === 4 || $abo_id === 17 || $abo_id === 18){
                if (!$maxReport){
                    $PartnextInfNumber = '0001-' . $anio . '-UNPRG-OAJ';
                    $result = $PartnextInfNumber;
                }else{
                    // Extraer el número y el año del máximo tra_number
                    $parts = explode('-', $maxReport);
                    $currentNumber = intval($parts[0]);
                    $year = $parts[1];

                    // Incrementar el número en uno
                    $nextNumber = $currentNumber + 1;

                    // Formatear el nuevo número
                    $PartnextInfNumber = sprintf("%04d", $nextNumber) . $parteEspecifica;
                    $result = $PartnextInfNumber;
                }
                
                if($abo_id === 4){
                    $maxReportExt = $reports->max('rep_ext_informe');
                    if(!$maxReportExt){
                        $PartnextInfNumber = '0001-' . $anio . '-UNPRG-OAJ-JCTO';
                        $result = $result . '-' . $PartnextInfNumber;
                    }else{
                        // Extraer el número y el año del máximo tra_number
                        $parts = explode('-', $maxReportExt);
                        $currentNumber = intval($parts[0]);
                        $year = $parts[1];

                        // Incrementar el número en uno
                        $nextNumber = $currentNumber + 1;

                        // Formatear el nuevo número
                        $nextInfNumber = sprintf("%04d", $nextNumber) . $parteEspecifica;
                        $result = $result . '-' . $nextInfNumber. '-JCTO';
                    }
                }
            }else{
                // Si no hay registros del año actual, inicializar el número de secuencia
                if (!$maxReport) {
                    //Iniciales
                    $nombres = $abogado->persona->nat_nombres . ' ' . $abogado->persona->nat_apellido_paterno . ' ' . $abogado->persona->nat_apellido_materno;
                    $palabras = explode(" ", $nombres);
                    $iniciales = "";
                    
                    // Iterar sobre cada palabra
                    foreach ($palabras as $palabra) {
                        // Verificar si la palabra no está vacía
                        if (strlen($palabra) > 0) {
                            // Obtener la primera letra de la palabra y agregarla a las iniciales
                            $iniciales .= strtoupper(substr($palabra, 0, 1));
                        }
                    }
                    $result = '0001-' . $anio . '-UNPRG-OAJ-'. $iniciales;
                }else{
                    // Extraer el número y el año del máximo tra_number
                    $parts = explode('-', $maxReport);
                    $currentNumber = intval($parts[0]);
                    $year = $parts[1];

                    // Incrementar el número en uno
                    $nextNumber = $currentNumber + 1;

                    // Formatear el nuevo número
                    $result = sprintf("%04d", $nextNumber) . $parteEspecifica;
                }
                
            }

            return response()->json([
                'state' => 'success',
                'abo_id'=> $abo_id,
                'result' => $result],
                200);

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
