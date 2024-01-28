<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Uuid;

class AlertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index()
    {
        try {
            $alertas =\App\Models\Alert::obtenerAlertasFaltantes();
            return response()->json(['state' => 0, 'data' => $alertas], 200);
        } catch (\Exception $e) {
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }


    protected function store(Request $request)
    {
        try {
            DB::beginTransaction();
    
            $fechaVencimiento = $request->ale_fecha_vencimiento;
            $descripcion = $request->ale_descripcion;
            $expId = $request->exp_id;
    
            $auFecha = strtoupper(trim($fechaVencimiento));
            $hoy = date('Y-m-d');
    
            $diasFaltantes = (new DateTime($auFecha))->diff(new DateTime($hoy))->days;
    
            $alert = \App\Models\Alert::create([
                'ale_fecha_vencimiento' => $fechaVencimiento,
                'ale_descripcion' => $descripcion,
                'exp_id' => $expId,
                'ale_dias_faltantes' => $diasFaltantes,
            ]);
    
            DB::commit();
    
            return response()->json(['state' => 0, 'data' => $alert], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }
    
}
