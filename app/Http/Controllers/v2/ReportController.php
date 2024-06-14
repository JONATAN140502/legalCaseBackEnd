<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Proceeding;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class ReportController extends Controller
{

    public function executionAmounts()
    {

        try {
            DB::beginTransaction();
            $user_id = Auth::user()->id;

            $report = [
                'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
                'rep_tipo' => 'MONTOS EN EJECUCION',
                'usu_id' => $user_id
            ];

            $report = Report::create($report);

            $audit = [
                'accion' => 'GENERACION DE REPORTE',
                'model' => Report::class,
                'model_id' => $report->id,
                'user_id' => $user_id
            ];

            $audit = Audit::create($audit);

            $proceedings = Proceeding::latest()
                ->with(['procesal.persona', 'materia', 'pretension', 'montos'])
                ->where('exp_estado_proceso', 'EN EJECUCION')
                ->where('type_id', 1)
                ->get();

            $proceedings->transform(function ($item) {
                $item['exp_fecha_inicio'] = $this->formatDate($item->exp_fecha_inicio);
                return $item;
            });

            $totalAmountSentence = 0;
            $totalBalancePayable = 0;

            foreach ($proceedings as $proceeding) {
                if ($proceeding->montos) {
                    foreach ($proceeding->montos as $amount) {
                        $totalAmountSentence += $amount->total_amount_sentence ?? 0;
                        $totalBalancePayable += $amount->total_balance_payable ?? 0;
                    }
                }
            }

            $amounts = [
                'total_amount_sentence' => $totalAmountSentence,
                'total_balance_payable' => $totalBalancePayable
            ];

            $pdf = PDF::loadView('executionAmounts', compact('proceedings', 'amounts'))->setPaper('a4', 'landscape');
            DB::commit();
            return $pdf->download('executionAmounts.pdf');

            // return response()->json($proceedings, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['exception' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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

    private function formatDate($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y');
    }
}
