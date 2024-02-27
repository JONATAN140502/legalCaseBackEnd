<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Address;
use App\Models\History;
use App\Models\Proceeding;
use App\Models\Procesal;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Add this line to import DB

class PersonController extends Controller
{
    protected $personModel;

    public function __construct(Person $personModel)
    {
        $this->middleware('auth');
        $this->personModel = $personModel;
    }

    protected function index()
    {
        try {
            $personas = Person::with(['procesal'])
                ->orderByDesc('updated_at')
                ->get();

            if ($personas->isEmpty()) {
                return response()->json(['message' => 'No se encontraron personas.'], 404);
            }

            return response()->json(['data' => $personas], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud.'], 500);
        }
    }

    //traer los demandados
    protected function indexdemandados(Request $request)
    {
        $data = Person::orderBy('created_at', 'DESC')
            ->where('tipo_procesal', 'DEMANDADO')
            ->whereHas('procesal.expediente', function ($query) {
                $query->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION']);
            })
            ->get();


        return response()->json(['data' => $data], 200);
    }

    //Traer Demandados, demandantes y nuevos clientes para el oficio
    protected function indexPersons()
    {
        $tiposProcesalesPermitidos = ['DEMANDADO', 'DEMANDANTE', 'CLIENTE'];

        $data = Person::orderBy('updated_at', 'DESC')
            ->whereIn('tipo_procesal', $tiposProcesalesPermitidos)
            ->get();
        return response()->json(['data' => $data], 200);
    }

    protected function traerExpedientes(Request $request)
    {
        try {
            DB::beginTransaction();
            $doc = $request->documento;
            $tipoPersona = null;
            $persona = null;
            $procesales = [];
            if (strlen($doc) === 8) {
                $tipoPersona = "NATURAL";
                $persona = Person::where('nat_dni', $doc)->first();
            } else {
                $tipoPersona = "JURIDICA";
                $persona = Person::where('jur_ruc', $doc)->first();
            }
            $procesales = Procesal::where('per_id', $persona->per_id)->get();
            $personaData = [];
            if ($tipoPersona == "NATURAL") {
                $personaData = [
                    'per_id' => $persona->per_id,
                    'documento' => $persona->nat_dni,
                    'nat_nombres' => ucwords(strtolower($persona->nat_nombres)),
                    'nat_apellido_paterno' => ucfirst(strtolower($persona->nat_apellido_paterno)),
                    'nat_apellido_materno' => ucfirst(strtolower($persona->nat_apellido_materno)),
                    'condicion' => $persona->per_condicion,
                ];
            } else {
                $personaData = [
                    'per_id' => $persona->per_id,
                    'jur_id' => $persona->jur_id,
                    'jur_razon_social' => $persona->jur_razon_social,
                    'documento' => $persona->jur_ruc,
                    'condicion' => $persona->per_condicion,
                ];
            }
            $expedientesData = [];
            foreach ($procesales as $procesal) {
                $expediente = Proceeding::where('exp_id', $procesal->exp_id)->first();
                $expedientesData[] = [
                    'exp_id' => $expediente->exp_id,
                    'exp_numero' => $expediente->exp_numero,
                ];
            }

            DB::commit();

            $response = [
                'state' => 0,
                'persona' => $personaData,
                'tipo_persona' => $tipoPersona,
                'expedientes' => $expedientesData,
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function traerExpedientesDemandado(Request $request)
    {
        try {
            DB::beginTransaction();
            $doc = $request->documento;
            $tipoPersona = null;
            $persona = null;
            $procesales = [];
            if (strlen($doc) === 8) {
                $tipoPersona = "NATURAL";
                $persona = Person::where('nat_dni', $doc)->first();
            } else {
                $tipoPersona = "JURIDICA";
                $persona = Person::where('jur_ruc', $doc)->first();
            }
            $procesales = Procesal::where('per_id', $persona->per_id)->get();
            $personaData = [];
            if ($tipoPersona == "NATURAL") {
                $personaData = [
                    'per_id' => $persona->per_id,
                    'documento' => $persona->nat_dni,
                    'nat_nombres' => $persona->nat_nombres,
                    'nat_apellido_paterno' => $persona->nat_apellido_paterno,
                    'nat_apellido_materno' => $persona->nat_apellido_materno,
                    'condicion' => $persona->per_condicion,
                ];
            } else {
                $personaData = [
                    'per_id' => $persona->per_id,
                    'jur_id' => $persona->jur_id,
                    'jur_razon_social' => $persona->jur_razon_social,
                    'documento' => $persona->jur_ruc,
                    'condicion' => $persona->per_condicion,
                ];
            }
            $expedientesData = [];
            foreach ($procesales as $procesal) {
                $expediente = Proceeding::where('exp_id', $procesal->exp_id)->first();
                $expedientesData[] = [
                    'exp_id' => $expediente->exp_id,
                    'exp_numero' => $expediente->exp_numero,
                ];
            }

            DB::commit();

            $response = [
                'state' => 0,
                'persona' => $personaData,
                'tipo_persona' => $tipoPersona,
                'expedientes' => $expedientesData,
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function detalleDemandante($id)
    {
        $tipo_persona = null;

        $person = Person::where('per_id', $id)->first();
        if ($person->nat_dni != null) {
            $tipo_persona = 'NATURAL';
        } else {
            $tipo_persona = 'JURIDICA';
        }
        // if (strlen($doc) === 8) {
        //     $person = Person::where('nat_dni', $doc)->first();
        //     $tipo_persona = 'NATURAL';
        // } else {
        //     $person = Person::where('jur_ruc', $doc)->first();
        //     $tipo_persona = 'JURIDICA';
        // }

        if (!$person) {
            return response()->json(['state' => 1, 'message' => 'Persona no encontrada'], 404);
        }

        $procesal = Procesal::where('per_id', $person->per_id)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$procesal) {
            return response()->json(['state' => 1, 'message' => 'Procesal no encontrado para la persona'], 404);
        }

        $expediente = $procesal->expediente;
        $direccion = Address::where('per_id', $person->per_id)
            ->with('district.province.departament')
            ->first();

        if (!$expediente) {
            return response()->json(['state' => 1, 'message' => 'Expediente no encontrado para el proceso'], 404);
        }

        // Construye la respuesta con la estructura deseada
        $data = [
            'data' => [
                'expediente' => [
                    'exp_id' => $expediente->exp_id,
                    'exp_numero' => $expediente->exp_numero,
                    'multiple' => $expediente->multiple,
                ],
                'persona' => [
                    'tipo_persona' => $tipo_persona,
                    'nat_dni' => $tipo_persona === 'NATURAL' ? $person->nat_dni : null,
                    'nat_apellido_paterno' => $tipo_persona === 'NATURAL' ? ucwords(strtolower($person->nat_apellido_paterno)) : null,
                    'nat_apellido_materno' => $tipo_persona === 'NATURAL' ? ucwords(strtolower($person->nat_apellido_materno)) : null,
                    'nat_nombres' => $tipo_persona === 'NATURAL' ? ucwords(strtolower($person->nat_nombres)) : null,
                    'nat_telefono' => $tipo_persona === 'NATURAL' ? $person->nat_telefono : null,
                    'nat_correo' => $tipo_persona === 'NATURAL' ? strtolower($person->nat_correo) : null,
                    'jur_ruc' => $tipo_persona === 'JURIDICA' ? $person->jur_ruc : null,
                    'jur_razon_social' => $tipo_persona === 'JURIDICA' ? ucwords(strtolower($person->jur_razon_social)) : null,
                    'jur_telefono' => $tipo_persona === 'JURIDICA' ? $person->jur_telefono : null,
                    'jur_correo' => $tipo_persona === 'JURIDICA' ? strtolower($person->jur_correo) : null,
                ],
                'direccion' => [
                    'dir_calle_av' => ucwords(strtolower($direccion->dir_calle_av)),
                    'dis_nombre' => ucwords(strtolower($direccion->district->dis_nombre)),
                    'pro_nombre' => ucwords(strtolower($direccion->district->province->pro_nombre)),
                    'dep_nombre' => ucwords(strtolower($direccion->district->province->departament->dep_nombre)),
                ]
            ],
        ];

        return response()->json($data, 200);
    }



    protected function detalleDemandado($doc)
    {
        $person = $this->getPersonByDocument($doc);

        if (!$person) {
            return response()->json(['state' => 1, 'message' => 'Persona no encontrada'], 404);
        }

        $proceedings = Proceeding::where('exp_demandado', $person->per_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $data = $proceedings->map(function ($proceeding) use ($person) {
            $tipo_persona = null;
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'exp_numero' => $proceeding->exp_numero,
            ];

            if ($person->nat_id !== null) {
                $personData = $person->persona;
                $tipo_persona = 'natural';
            } elseif ($person->jur_id !== null) {
                $personData = $person->juridica;
                $tipo_persona = 'juridica';
            }

            $address = Address::where('per_id', $person->per_id)
                ->with('district.province.departament')
                ->first();

            $personDataArray = [];
            $addressDataArray = [];

            if ($tipo_persona === 'natural') {
                $personDataArray = [
                    'nat_dni' => $personData->nat_dni,
                    'nat_apellido_paterno' => ucwords(strtolower($personData->nat_apellido_paterno)),
                    'nat_apellido_materno' => ucwords(strtolower($personData->nat_apellido_materno)),
                    'nat_nombres' => ucwords(strtolower($personData->nat_nombres)),
                    'nat_telefono' => $personData->nat_telefono,
                    'nat_correo' => strtolower($personData->nat_correo),
                ];
            } elseif ($tipo_persona === 'juridica') {
                $personDataArray = [
                    'jur_ruc' => $personData->jur_ruc,
                    'jur_azon_social' => ucwords(strtolower($personData->jur_razon_social)),
                    'jur_telefono' => $personData->jur_telefono,
                    'jur_correo' => strtolower($personData->jur_correo),
                ];
            }

            if ($address) {
                $addressDataArray = [
                    'dir_calle_av' => ucwords(strtolower($address->dir_calle_av)),
                    'dis_nombre' => ucwords(strtolower($address->district->dis_nombre)),
                    'pro_nombre' => ucwords(strtolower($address->district->province->pro_nombre)),
                    'dep_nombre' => ucwords(strtolower($address->district->province->departament->dep_nombre)),
                ];
            }

            $result = array_merge($commonData, $personDataArray, $addressDataArray, ['tipo_persona' => $tipo_persona]);
            return $result;
        });

        return response()->json(['data' => $data->first()], 200);
    }

    public function updateDni(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validar los datos recibidos
            $request->validate([
                'per_id' => 'required|exists:persons,per_id',
                'newDni' => 'required|numeric|digits:8',
            ]);

            $id = $request->per_id;
            $persona = Person::where('per_id', $id)->first(); // Use first() instead of get()

            if (!$persona) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            $persona->nat_dni = $request->newDni;
            $persona->save();

            DB::commit();
            return response()->json(['state' => 'success']);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            // Captura los errores de validación
            $errors = $validationException->errors();
            return response()->json(['error' => $errors], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['state' => '1', 'exception' => (string) $e]);
        }
    }


    protected function getPersonByDocument($doc)
    {
        if (strlen($doc) === 8) {
            $persona = Person::where('nat_dni', $doc)->first();
            return $persona ? Procesal::where('per_id', $persona->per_id)->first() : null;
        } else {
            $persona = Person::where('jur_ruc', $doc)->first();
            return $persona ? Procesal::where('per_id', $persona->per_id)->first() : null;
        }
    }

    protected function getHistoryByDocument($doc)
    {
        try {
            $person = $this->getPersonByDocument($doc);

            if ($person) {
                $history = History::where('per_id', $person->per_id)
                    ->with('expediente')
                    ->orderBy('created_at', 'ASC')
                    ->get();

                // Filtrar los campos que deseas
                $filteredHistory = $history->map(function ($item) {
                    return [
                        'his_id' => $item->his_id,
                        'his_fecha_hora' => $item->his_fecha_hora,
                        'his_medio_comuniacion' => $item->his_medio_comuniacion,
                        'his_detalle' => $item->his_detalle,
                        'exp_id' => $item->expediente->exp_id,
                        'exp_numero' => $item->expediente->exp_numero,
                    ];
                });

                return response()->json(['data' => $filteredHistory]);
            } else {
                return response()->json(['data' => []]); // Retorna un arreglo vacío si no hay datos
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function salir(Request $request)
    {
        try {
            \App\Models\Audit::create([
                'accion' => 'Salio del Sistema',
                'model' => '\App\Models\User',
                'model_id' => Auth::user()->id,
                'user_id' => Auth::user()->id,
            ]);
            if (Auth::check()) {
                Auth::user()->token()->revoke();
            }
            return \response()->json(['state' => 0, 'message' => 'cierre  de sesión correctamente'], 200);
        } catch (Exception $e) {
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
}
