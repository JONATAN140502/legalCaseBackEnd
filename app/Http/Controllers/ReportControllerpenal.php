<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Uuid;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Resources\{
    LawyerResource
};
use App\Models\Audit;

class ReportControllerpenal extends Controller
{
    protected function inicio(Request $request)
    {
    }
    protected function inicioAdmin(Request $request)
    {
        $exp = Audit::orderBy('created_at', 'DESC')
            ->with(['user' => function ($query) {
                $query->withTrashed();
            }, 'exp'])
            ->get();
        $today = date("Y-m-d");  //hoy
        $expTotal = \App\Models\Proceeding::whereIn(
            'exp_estado_proceso',
            [
                'EN TRAMITE',
                'EN EJECUCION'
            ]
        )->count();

        $alerts = \App\Models\Alert::where('ale_fecha_vencimiento', '>=', $today)
            ->count();
        $audiences = \App\Models\Audience::where('au_fecha', '>=', $today)
            ->count();
        $combinedData = [
            'expTotal' => $expTotal,
            'alerts' => $alerts,
            'audiences' => $audiences
        ];
        return response()->json([
            'state' => 0, 'data' => $exp, 'count' => $combinedData
        ], 200);
    }

    protected function pdfexptramite(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE EN TRAMITE/AUTOMATIZADO',
            'usu_id' => \Auth::user()->id,
        ]);

        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => \Auth::user()->id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_estado_proceso', 'EN TRAMITE')
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id', 2)
            ->get();

        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => isset($proceeding->pretension->pre_nombre) ? $proceeding->pretension->pre_nombre : '-',
                'materia' => isset($proceeding->materia->mat_nombre) ? $proceeding->materia->mat_nombre : '-',
                'especialidad' => isset($proceeding->specialty->esp_nombre) ? $proceeding->specialty->esp_nombre : '-',
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }
        $tipo = ' Reporte de Expedientes Penales en Trámite';
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('penal/pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    protected function pdfexparchivados(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE EN TRAMITE/AUTOMATIZADO',
            'usu_id' => \Auth::user()->id,
        ]);

        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => \Auth::user()->id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_estado_proceso', 'ARCHIVADO')
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id', 2)
            ->get();

        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => isset($proceeding->pretension->pre_nombre) ? $proceeding->pretension->pre_nombre : '-',
                'materia' => isset($proceeding->materia->mat_nombre) ? $proceeding->materia->mat_nombre : '-',
                'especialidad' => isset($proceeding->specialty->esp_nombre) ? $proceeding->specialty->esp_nombre : '-',
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }
        $tipo = ' Reporte de Expedientes Penales Archivados';
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('penal/pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    protected function pdfexpejecucion(Request $request)
    {

        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE EN TRAMITE/AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_estado_proceso', 'EN EJECUCION')
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id', 2)
            ->get();

        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => isset($proceeding->pretension->pre_nombre) ? $proceeding->pretension->pre_nombre : '-',
                'materia' => isset($proceeding->materia->mat_nombre) ? $proceeding->materia->mat_nombre : '-',
                'especialidad' => isset($proceeding->specialty->esp_nombre) ? $proceeding->specialty->esp_nombre : '-',
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }
        $tipo = "Reporte de Expedientes Penales en Ejecución";

        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('penal/pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    protected function pdfexps(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTES TOTAL/AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->whereIn(
                'exp_estado_proceso',
                ['EN TRAMITE', 'EN EJECUCION']
            )
            ->where('type_id', 2)
            ->get();

        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => isset($proceeding->pretension->pre_nombre) ? $proceeding->pretension->pre_nombre : '-',
                'materia' => isset($proceeding->materia->mat_nombre) ? $proceeding->materia->mat_nombre : '-',
                'especialidad' => isset($proceeding->specialty->esp_nombre) ? $proceeding->specialty->esp_nombre : '-',
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }
        $tipo = "Reporte del Total de Expedientes Penales";
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);
        return \PDF::loadView('penal/pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    protected function pdffechaaño(Request $request)

    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  MES Y AÑO /PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $mes = $request->mes;
        $año = $request->año;
        $mes = intval($mes);
        if ($mes >= 1 && $mes <= 9) {
            $mesFormateado = '0' . $mes;
        } else {
            $mesFormateado = (string) $mes;
        }
        $fechaBuscada = $año . '-' . $mesFormateado;
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_fecha_inicio', 'LIKE', $fechaBuscada . '%')
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id', 2)
            ->get();

        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => isset($proceeding->pretension->pre_nombre) ? $proceeding->pretension->pre_nombre : '-',
                'materia' => isset($proceeding->materia->mat_nombre) ? $proceeding->materia->mat_nombre : '-',
                'especialidad' => isset($proceeding->specialty->esp_nombre) ? $proceeding->specialty->esp_nombre : '-',
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }
        $tipo = "Reporte del  de Expedientes Penales de Mes y Año";
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('penal/pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    protected function pdfexpsabogado(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  ABOGADO /PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $abogado =
            \App\Models\Lawyer::where('abo_id', $request->abo_id)
            ->with('persona')->first();
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('abo_id', $request->abo_id)
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id', 2)
            ->get();
        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => isset($proceeding->pretension->pre_nombre) ? $proceeding->pretension->pre_nombre : '-',
                'materia' => isset($proceeding->materia->mat_nombre) ? $proceeding->materia->mat_nombre : '-',
                'especialidad' => isset($proceeding->specialty->esp_nombre) ? $proceeding->specialty->esp_nombre : '-',
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }
        $tipo = 'Expedientes Penales  a cargo de:' . $abogado->persona->nat_nombres . '
            ' . $abogado->persona->nat_apellido_paterno . ' ' . $abogado->persona->nat_apellido_materno;
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('penal/pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
  
    protected function pdfpretension(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  PRETENSION /PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_pretencion', $request->exp_pretension)
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id', 2)
            ->get();
        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => isset($proceeding->pretension->pre_nombre) ? $proceeding->pretension->pre_nombre : '-',
                'materia' => isset($proceeding->materia->mat_nombre) ? $proceeding->materia->mat_nombre : '-',
                'especialidad' => isset($proceeding->specialty->esp_nombre) ? $proceeding->specialty->esp_nombre : '-',
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }
        $pre = \App\Models\Claim::find($request->exp_pretension);
        $tipo = "Reporte de Expedientes Penales por Delito:" . $pre->pre_nombre;
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('penal/pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    protected function pdffechas(Request $request)

    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  DESDE-HASTA/PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereBetween('exp_fecha_inicio', [$request->fechaDesde, $request->fechaHasta])
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id',2)
            ->get();
        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => isset($proceeding->pretension->pre_nombre) ? $proceeding->pretension->pre_nombre : '-',
                'materia' => isset($proceeding->materia->mat_nombre) ? $proceeding->materia->mat_nombre : '-',
                'especialidad' => isset($proceeding->specialty->esp_nombre) ? $proceeding->specialty->esp_nombre : '-',

                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }
        $tipo = "Reporte de Expedientes Penales del:" . date('d-m-Y', strtotime($request->fechaDesde)) . ' al 
            ' . date('d-m-Y', strtotime($request->fechaHasta));
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);
        return \PDF::loadView('penal/pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    public function contarExpedientesPorAnio(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  EXPEDIENTES POR AÑO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $data = \App\Models\Proceeding::selectRaw('YEAR(exp_fecha_inicio) as year, COUNT(*) as cantidad')
            ->groupBy(DB::raw('YEAR(exp_fecha_inicio)'))
            ->orderBy(DB::raw('YEAR(exp_fecha_inicio)'))
            ->where('type_id',2)
            ->get();
        //  return response()->json(['state' => 0, 'data' => $data], 200);

        return \PDF::loadView('penal/graficodebarras', compact('data'))
            ->download();
    }
    public function contarExpedientesPorAboTipo(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  EXPEDIENTE DE ABOGADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $data1 = \App\Models\Proceeding::selectRaw('abo_id, COUNT(*) as cantidad')
            ->groupBy('abo_id')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->where('type_id',2)
            ->get();
        $data = $data1->map(function ($abo) {
            $abogado = \App\Models\Lawyer::find($abo->abo_id);
            if ($abogado) {
                $nombreAbogado = $abogado->persona->nat_apellido_paterno . ' ' . $abogado->persona->nat_apellido_materno . ' ' . $abogado->persona->nat_nombres;

                return [
                    'name' => $nombreAbogado,
                    'cantidad' => $abo->cantidad,
                ];
            }
            return null;
        })->filter();
        $labels = json_encode($data->pluck('name')->toArray(), JSON_UNESCAPED_UNICODE);
        $values = $data->pluck('cantidad')->implode(',');
        $chartUrl = "https://quickchart.io/chart?c={
            type: 'pie',
            data: {
            labels:  {$labels},
            datasets: [
                {
                data: [{$values}],
                backgroundColor: [
                    'red',
                    'blue',
                    'green',
                    'orange',
                    'purple',
                    'yellow',
                    'pink',
                    'cyan'
                ],
                },
            ]
            },
            options: {
            plugins: {
                datalabels: {
                color: 'black',
                font: {
                    weight: 'bold',
                    family: 'Arial'
                }
                }
            }
            }
        }";

        $pdf = PDF::loadView('penal/graficodetorta', compact('chartUrl'));
        return $pdf->download();
    }
    //formatear los procesales
    protected function formatProcesalData($procesal)
    {
        $processedProcesals = [];

        foreach ($procesal as $procesalItem) {
            $data = [
                'proc_id' => $procesalItem->proc_id,
                'per_id' => $procesalItem->per_id,
                'tipo_procesal' => $procesalItem->tipo_procesal,
                'tipo_persona' => $procesalItem->tipo_persona,
            ];

            if ($procesalItem->tipo_persona === 'NATURAL') {
                $data = array_merge($data, [
                    'dni' => $procesalItem->persona->nat_dni,
                    'apellido_paterno' => ucwords(strtolower($procesalItem->persona->nat_apellido_paterno)),
                    'apellido_materno' => ucwords(strtolower($procesalItem->persona->nat_apellido_materno)),
                    'nombres' => ucwords(strtolower($procesalItem->persona->nat_nombres)),
                    'telefono' => $procesalItem->persona->nat_telefono,
                    'correo' => strtolower($procesalItem->persona->nat_correo),
                    'condicion' => strtolower($procesalItem->persona->per_condicion),
                ]);
            } else {
                $data = array_merge($data, [
                    'ruc' => $procesalItem->persona->jur_ruc,
                    'razon_social' => $procesalItem->persona->jur_razon_social,
                    'telefono' => $procesalItem->persona->jur_telefono,
                    'correo' => strtolower($procesalItem->persona->jur_correo),
                    'condicion' => strtolower($procesalItem->persona->per_condicion),
                ]);
            }

            $processedProcesals[] = $data;
        }

        return $processedProcesals;
    }
}
