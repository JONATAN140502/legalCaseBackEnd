<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Proceeding;
use App\Models\ProceedingTypes;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProceedingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proceeding_types = ProceedingTypes::select('id', 'name')->get();
        return response()->json($proceeding_types);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    protected function criminalReports(Request $request)
    {
        // Creación del reporte
        $report = Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE PENALES',
            'usu_id' => Auth::user()->id,
        ]);

        // Registro de la acción de generación de reporte en el auditoría
        Audit::create([
            'accion' => 'GENERACIÓN DE REPORTE',
            'model' => get_class(new Report()), // Obtiene el nombre completo del modelo
            'model_id' => Auth::user()->id,
            'user_id' => Auth::user()->id,
        ]);

        // Obtención de los expedientes en trámite
        $proceedings = Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_estado_proceso', 'EN TRAMITE')
            ->where('type_id', '2')
            ->with('procesal.persona', 'pretension', 'materia', 'specialty')
            ->get();

        // Formateo de los datos de los expedientes
        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'delito' => optional($proceeding->pretension)->pre_nombre ?? '-',
                // 'distrito_judicial' => optional($proceeding->materia)->mat_nombre ?? '-',
                // 'especialidad' => optional($proceeding->specialty)->esp_nombre ?? '-',
                // 'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }

        // División de datos para la generación de múltiples páginas en PDF
        $totalRegistros = count($formattedData);
        $quinto = ceil($totalRegistros / 5);
        $data1 = array_slice($formattedData, 0, $quinto);
        $data2 = array_slice($formattedData, $quinto, $quinto);
        $data3 = array_slice($formattedData, $quinto * 2, $quinto);
        $data4 = array_slice($formattedData, $quinto * 3, $quinto);
        $data5 = array_slice($formattedData, $quinto * 4, $quinto);

        // Generación y descarga del PDF
        $tipo = 'Reporte de Expedientes en Trámite';
        $pdf = \PDF::loadView('pdfExpedienteTramite', compact('data1', 'data2', 'data3', 'data4', 'data5', 'tipo'));
        return $pdf->download();
    }
}
