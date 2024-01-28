<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Uuid;

use App\Models\Person;
use App\Models\Proceeding;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index(Request $request)
    {
        try {
            $historiales = \App\Models\History::with('person.juridica', 'person.persona', 'expediente')->get();

            $data = $historiales->map(function ($historial) {
                $person = $historial->person;
                $expediente = $historial->expediente;
                $tipo_persona = null;

                $commonData = [
                    'his_id' => $historial->his_id,
                    'his_fecha_hora' => $historial->his_fecha_hora,
                    'his_medio_comuniacion' => $historial->his_medio_comuniacion,
                    'his_detalle' => $historial->his_detalle,
                    'exp_id' => $historial->exp_id,
                    'exp_numero' => $expediente->exp_numero,          
                ];
                if($expediente->exp_demandante){
                    $commonData += ['procesal' =>'demandante',    
                    ];
                 }
                 elseif($expediente->exp_demandado){
                    $commonData += [
                        'procesal' =>'demandado',    
                    ];
                 }

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
                        'nat_dni' => $personData->nat_dni,
                        'nat_apellido_paterno' => $personData->nat_apellido_paterno,
                        'nat_apellido_materno' => $personData->nat_apellido_materno,
                        'nat_nombres' => $personData->nat_nombres,
                        'nat_telefono' => $personData->nat_telefono,
                        'nat_correo' => $personData->nat_correo,
                    ];
                } elseif ($tipo_persona === 'juridica') {
                    $personDataArray = [
                        'nat_ruc' => $personData->jur_ruc,
                        'nat_razon_social' => $personData->jur_razon_social,
                        'nat_telefono' => $personData->jur_telefono,
                        'nat_correo' => $personData->jur_correo,
                    ];
                } else {
                    $personDataArray = [];
                }
                return array_merge($commonData, $personDataArray, ['tipo_persona' => $tipo_persona]);
            });
            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['state' => 1, 'exception' => (string) $e], 500);
        }
    }

    protected function store(Request $request)
    {
        $transaction = DB::transaction(function () use ($request) {
            $data = [
                'per_id' => trim($request->per_id),
                'exp_id' => strtoupper(trim($request->exp_id)),
                'his_fecha_hora' => strtoupper(trim($request->his_fecha_hora)),
                'his_medio_comuniacion' => $request->his_medio_comuniacion,
                'his_detalle' => $request->his_detalle,
            ];

            $his = \App\Models\History::create($data);

            return $his;
        });

        try {
            return response()->json(['state' => 0, 'data' => $transaction], 200);
        } catch (Exception $e) {
            return ['state' => 1, 'exception' => (string) $e];
        }
    }

}
