<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Proceeding;
use App\Models\Report;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReportController extends Controller
{

    public function executionAmounts()
    {

        try {

            $user_id = Auth::user()->id;

            $report['rep_fecha_generacion'] = now()->setTimezone('America/Lima');
            $report['rep_tipo'] = 'MONTOS EN EJECUCION';
            $report['usu_id'] = $user_id;

            $report = Report::create($report);

            $audit['accion'] = 'GENERACION DE REPORTE';
            $audit['model'] = Report::class;
            $audit['model_id'] = $user_id;
            $audit['user_id'] = $user_id;

            $audit = Audit::create($audit);

            $proceedings = Proceeding::latest()
                ->with('procesal.persona', 'materia', 'pretension', 'montos')
                ->whereIn('exp_estado_proceso', ['EN EJECUCION'])
                ->where('type_id', 2)
                ->get();

            $totalAmountSentence = 0;
            $totalBalancePayable = 0;

            foreach ($proceedings as $proceeding) {
                foreach ($proceeding->montos as $amount) {
                    $$totalAmountSentence += $amount->$totalAmountSentence;
                    $totalBalancePayable += $amount->$totalBalancePayable;
                }
            }

            $amounts = [
                'total_amount_sentence' => $totalAmountSentence,
                'total_balance_payable' => $totalBalancePayable
            ];

            $pdf = \PDF::loadView('executionAmounts', compact('proceedings', 'amounts'));

            // Descargar el PDF
            return $pdf->download('executionAmounts.pdf');

        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
}
