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
use App\Models\Alert;
use App\Models\Audience;
use App\Models\Audit;
use App\Models\Proceeding;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    protected function inicio(Request $request)
    {
    }
    protected function inicioAdmin(Request $request)
    {
        $audit = Audit::with(['user' => function ($query) {
            $query->withTrashed();
        }, 'exp'])
            ->latest()
            ->take(6)
            ->get();
        $proceeding = Proceeding::latest()->take(6)->get();

        // Obtener la fecha de hoy
        $today = now()->format('Y-m-d');

        // Contar el total de expedientes en trámite o en ejecución
        $expTotal = Proceeding::whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])->count();

        // Contar las alertas con fecha de vencimiento mayor o igual a la fecha de hoy
        $alerts = Alert::where('ale_fecha_vencimiento', '>=', $today)->count();

        // Contar las audiencias con fecha mayor o igual a la fecha de hoy
        $audiences = Audience::where('au_fecha', '>=', $today)->count();

        // Crear el arreglo combinado de datos
        $combinedData = compact('expTotal', 'alerts', 'audiences');

        // Devolver una respuesta JSON con el estado, los datos de auditoría, y los datos combinados
        return response()->json([
            'state' => 0, // Supongo que 0 indica un estado correcto, ajusta según necesites
            'audit' => $audit,
            'count' => $combinedData,
            'proceeding' => $proceeding
        ], 200);
    }


    protected function exprecientes(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('person.juridica', 'person.persona')

            ->take(5)
            ->get();


        $data = $proceedings->map(function ($proceeding) {
            $procesal = null;
            $tipo_persona = null;
            if ($proceeding) {
                if ($proceeding->exp_demandante !== null) {
                    $person = $proceeding->demandante;
                    $procesal = 'demandante';
                } elseif ($proceeding->exp_demandado !== null) {
                    $person = $proceeding->demandado;
                    $procesal = 'demandado';
                }
            }
            $fecha_inicio = $proceeding->exp_fecha_inicio;
            $fecha_formateada = date('d-m-Y', strtotime($fecha_inicio));
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => $fecha_formateada,
                'pretencion' => ucwords(strtolower($proceeding->exp_pretencion)),
                'materia' => ucwords(strtolower($proceeding->exp_materia)),
                'especialidad' => ucwords(strtolower($proceeding->exp_especialidad)),
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'procesal' => $procesal
            ];
            if ($person) {
                if ($person->nat_id !== null) {
                    $personData = $person->persona;
                    $tipo_persona = 'natural';
                } elseif ($person->jur_id !== null) {
                    $personData = $person->juridica;
                    $tipo_persona = 'juridica';
                }
            }

            if ($tipo_persona === 'natural') {
                $personDataArray = [
                    'dni' => $personData->nat_dni,
                    'apellido_paterno' => ucwords(strtolower($personData->nat_apellido_paterno)),
                    'apellido_materno' => ucwords(strtolower($personData->nat_apellido_materno)),
                    'nombres' => ucwords(strtolower($personData->nat_nombres)),
                    'telefono' => $personData->nat_telefono,
                    'correo' => strtolower($personData->nat_correo),
                ];
            } elseif ($tipo_persona === 'juridica') {
                $personDataArray = [
                    'ruc' => ucwords(strtolower($personData->jur_ruc)),
                    'razon_social' => ucwords(strtolower($personData->jur_razon_social)),
                    'telefono' => $personData->jur_telefono,
                    'correo' => strtolower($personData->jur_correo),
                ];
            } else {
                $personDataArray = [];
            }

            return array_merge($commonData, $personDataArray, ['tipo_persona' => $tipo_persona]);
        });

        return response()->json(['data' => $data], 200);
    }
    protected function distritos(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address.district.province')
            ->get();

        $districts = $proceedings->pluck('person.address.district')->unique('dis_id')->map(function ($district) {
            if (isset($district['dis_id']) && isset($district['dis_nombre']) && isset($district['province']['pro_id']) && isset($district['province']['pro_nombre'])) {
                return [
                    'id_distrito' => $district['dis_id'],
                    'nombre' => $district['dis_nombre'],
                    'id_provincia' => $district['province']['pro_id'],
                    'provincia' => [
                        'id' => $district['province']['pro_id'],
                        'nombre' => $district['province']['pro_nombre'],
                    ],
                ];
            }
        })->filter()->values();

        $provinces = $districts->pluck('provincia')->unique('id')->values();
        return response()->json([
            'distritos' => $districts,
            'provincias' => $provinces,
        ], 200);
    }


    public function pdfabogados(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE ABOGADO/AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);

        $abogados = \App\Models\Lawyer::orderBy('created_at', 'DESC')->with('persona')->get();
        $pdf = PDF::loadView('vista_pdf_abo', ['data' => $abogados]);
        return $pdf->download('archivo.pdf');
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
            ->where('type_id',1)
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
        $tipo = ' Reporte de Expedientes Civil/Laboral en Trámite';
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
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
            ->where('type_id',1)
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
        $tipo = ' Reporte de Expedientes Civil/Laboral Archivados';
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
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
            ->where('type_id',1)
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
        $tipo = "Reporte de Expedientes  Civil/Laboral en Ejecución";

        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
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
            ->where('type_id',1)
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
        $tipo = "Reporte del Total de Expedientes  Civil/Laboral";
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);
        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    protected function pdfdemandantes(Request $request)
    {
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
            ->where('type_id',1)
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
        $tipo = "Reporte del  de Expedientes  Civil/Laboral de Mes y Año";
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }

    protected function pdfmateria(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  MATERIA /PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_materia', $request->exp_materia)
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id',1)
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
        $materia = \App\Models\Subject::find($request->exp_materia);
        $tipo = "Reporte de Expedientes   Civil/Laboral por Materia:" . $materia->mat_nombre;
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
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
            ->where('type_id',1)
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
        $tipo = 'Expedientes  Civil/Laboral a cargo de:' . $abogado->persona->nat_nombres . '
            ' . $abogado->persona->nat_apellido_paterno . ' ' . $abogado->persona->nat_apellido_materno;
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }


    protected function pdfpretenciones(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  PRETENCIONES /AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);

        $montos = null;
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('montos')
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id',1)
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
            $montos = $proceeding->montos;
            if ($montos) {
                $commonData += [
                    '$monto_ejecucion1' => $montos->ex_ejecucion_1 != null ? $proceeding->montos->ex_ejecucion_1 : '',
                    '$monto_ejecucion2' => $proceeding->montos->ex_ejecucion_2 != null ? $proceeding->montos->ex_ejecucion_2 : '',
                    '$interes1' => $proceeding->montos->ex_interes_1 != null ? $proceeding->montos->ex_interes_1 : '',
                    '$interes2' => $proceeding->montos->ex_interes_2 != null ? $proceeding->montos->ex_interes_2 : '',
                    '$costos' => $proceeding->montos->ex_costos != null ? $proceeding->montos->ex_costos : '',
                ];
            }
            $formattedData[] = $commonData;
        }
        $tipo = "Total de Pretensiones en Demanda  Civil/Laboral";
        return \PDF::loadView('pdfpretensiones', compact('formattedData', 'tipo'))
            ->download();
    }
    protected function pdfejecuciones(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  EJECUCIONES /AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        \App\Models\Audit::create([
            'accion' => 'GERENACION DE REPORTE',
            'model' => '\App\Models\Report',
            'model_id' => \Auth::user()->id,
            'user_id' => $request->usu_id,
        ]);
        $montos = null;
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('montos')
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->where('type_id',1)
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
            $montos = $proceeding->montos;
            if ($montos) {
                $commonData += [
                    '$monto_ejecucion1' => $montos->ex_ejecucion_1 != null ? $proceeding->montos->ex_ejecucion_1 : '',
                    '$monto_ejecucion2' => $proceeding->montos->ex_ejecucion_2 != null ? $proceeding->montos->ex_ejecucion_2 : '',
                    '$interes1' => $proceeding->montos->ex_interes_1 != null ? $proceeding->montos->ex_interes_1 : '',
                    '$interes2' => $proceeding->montos->ex_interes_2 != null ? $proceeding->montos->ex_interes_2 : '',
                    '$costos' => $proceeding->montos->ex_costos != null ? $proceeding->montos->ex_costos : '',
                ];
            }
            $formattedData[] = $commonData;
        }
        $tipo = "Total de Pretensiones a Pagar Civil/Laboral";

        return \PDF::loadView('pdfejecuciones', compact('formattedData', 'tipo'))
            ->download();
        // return response()->json(['state' => 0, 'data' => $formattedData], 200);

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
            ->where('type_id',1)
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
        $tipo = "Reporte de Expedientes  Civil/Laboral por Pretensión:" . $pre->pre_nombre;
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
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
            ->where('type_id',1)
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
        $tipo = "Reporte de Expedientes   Civil/Laboral del:" . date('d-m-Y', strtotime($request->fechaDesde)) . ' al 
            ' . date('d-m-Y', strtotime($request->fechaHasta));
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);
        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
    protected function pdfdistrito(Request $request)

    {
        $distrito = $request->distrito;
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  DESDE-HASTA/PERSONALIZADO',
            'usu_id' => $request->usu_id,
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
            ->with('person.address')
            ->with('person.juridica', 'person.persona')
            ->with('specialty')
            ->with('materia')
            ->get();
        $expedientesPorDistrito = $proceedings->filter(function ($item) use ($distrito) {
            return $item['person']['address']['district']['dis_id'] == $distrito;
        });
        $data = $expedientesPorDistrito->map(function ($proceeding) {
            $procesal = null;
            $tipo_persona = null;
            if ($proceeding) {
                if ($proceeding->exp_demandante !== null) {
                    $person = $proceeding->demandante;
                    $procesal = 'demandante';
                } elseif ($proceeding->exp_demandado !== null) {
                    $person = $proceeding->demandado;
                    $procesal = 'demandado';
                }
            }
            $fecha_inicio = $proceeding->exp_fecha_inicio;
            $fecha_formateada = date('d-m-Y', strtotime($fecha_inicio));
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => $fecha_formateada,
                'pretencion' => ucwords(strtolower($proceeding->exp_pretencion)),
                'materia' => ucwords(strtolower($proceeding->materia->mat_nombre)),
                'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'procesal' => $procesal
            ];
            if ($person) {
                if ($person->nat_id !== null) {
                    $personData = $person->persona;
                    $tipo_persona = 'natural';
                } elseif ($person->jur_id !== null) {
                    $personData = $person->juridica;
                    $tipo_persona = 'juridica';
                }
            }

            if ($tipo_persona === 'natural') {
                $personDataArray = [
                    'dni' => $personData->nat_dni,
                    'apellido_paterno' => ucwords(strtolower($personData->nat_apellido_paterno)),
                    'apellido_materno' => ucwords(strtolower($personData->nat_apellido_materno)),
                    'nombres' => ucwords(strtolower($personData->nat_nombres)),
                    'telefono' => $personData->nat_telefono,
                    'correo' => strtolower($personData->nat_correo),
                ];
            } elseif ($tipo_persona === 'juridica') {
                $personDataArray = [
                    'ruc' => ucwords(strtolower($personData->jur_ruc)),
                    'razon_social' => ucwords(strtolower($personData->jur_razon_social)),
                    'telefono' => $personData->jur_telefono,
                    'correo' => strtolower($personData->jur_correo),
                ];
            } else {
                $personDataArray = [];
            }

            return array_merge($commonData, $personDataArray, ['tipo_persona' => $tipo_persona]);
        });

        $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
        return $pdf->download('archivo.pdf');
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
            ->where('type_id',1)
            ->get();
        //  return response()->json(['state' => 0, 'data' => $data], 200);

        return \PDF::loadView('graficodebarras', compact('data'))
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
            ->where('type_id',1)
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

        $pdf = PDF::loadView('graficodetorta', compact('chartUrl'));
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

    protected function proceedingType(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE',
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
            ->where('type_id', $request->type)
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
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
        $typeProceeding = $request->type;
        if ($typeProceeding === '1') {
            $tipo = ' Reporte de Expedientes:  Civil / Laboral';
        } elseif ($typeProceeding === '2') {
            $tipo = ' Reporte de Expedientes:  Penal';
        } elseif ($typeProceeding === '3') {
            $tipo = ' Reporte de Expedientes:  Arbitral';
        }

        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        return \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'))
            ->download();
    }
}
