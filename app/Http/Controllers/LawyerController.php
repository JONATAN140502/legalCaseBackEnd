<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proceeding;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Uuid;
use App\Models\Person;
use App\Models\User;
use App\Models\Lawyer;
use App\Http\Requests\LawyerRequest;
use App\Http\Resources\LawyerResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class LawyerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Obtener todos los datos
    protected function index(Request $request)
    {
        $lawyers = \App\Models\Lawyer::orderBy('created_at', 'DESC')->with('persona')->get();

        $data = $lawyers->map(function ($lawyer) {
            return [
                'nat_correo' => $lawyer->persona->nat_correo,
                'abo_id' => $lawyer->abo_id,
                'abo_carga_laboral' => $lawyer->abo_carga_laboral,
                'abo_disponibilidad' => $lawyer->abo_disponibilidad,
                'per_id' => $lawyer->persona->per_id,
                'nat_dni' => $lawyer->persona->nat_dni,
                'nat_apellido_paterno' => ucwords(strtolower($lawyer->persona->nat_apellido_paterno)),
                'nat_apellido_materno' => ucwords(strtolower($lawyer->persona->nat_apellido_materno)),
                'nat_nombres' => ucwords(strtolower($lawyer->persona->nat_nombres)),
                'nat_telefono' => $lawyer->persona->nat_telefono,
            ];
        });

        return \response()->json(['data' => $data], 200);
    }

    protected function show(Request $request)
    {
        $Lawyer = \App\Models\Lawyer::where('abo_id', $request->abo_id)->with('persona')->first();
        $data = LawyerResource::collection([$Lawyer]);
        return \response()->json(['data' => $data], 200);
    }

    public function listTrades(Request $request)
    {
        try {
            $lawyer = Lawyer::findOrFail($request->abo_id);
            $trades = $lawyer->trades()->with('area')->get();
            return response()->json(['state' => 'success', 'data' => $trades], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validación de duplicados para DNI
            $existingDni = Person::withTrashed()
                ->where('nat_dni', $request->input('nat_dni'))
                ->first();

            if ($existingDni) {
                // Si ya existe una persona con el mismo DNI, devuelve un mensaje de error
                return response()->json([
                    'state' => 1,
                    'message' => 'Error al registrar abogado: La dni ya existe.',
                ], 422); // 422 Unprocessable Entity indica un error de validación
            }

            // Validación de duplicados para correo electrónico
            $existingEmail = Person::withTrashed()
                ->where('nat_correo', strtolower($request->input('nat_correo')))
                ->first();

            if ($existingEmail) {
                // Si ya existe una persona con el mismo correo electrónico, devuelve un mensaje de error
                return response()->json([
                    'state' => 1,
                    'message' => 'Error al registrar abogado: el correo electronico ya existe.',
                ], 422); // 422 Unprocessable Entity indica un error de validación
            }

            DB::beginTransaction();

            // Creación de la persona
            $persona = Person::create([
                'nat_dni' => $request->input('nat_dni'),
                'nat_apellido_paterno' => ucwords(strtolower($request->input('nat_apellido_paterno'))),
                'nat_apellido_materno' => ucwords(strtolower($request->input('nat_apellido_materno'))),
                'nat_nombres' => ucwords(strtolower($request->input('nat_nombres'))),
                'nat_telefono' => $request->input('nat_telefono'),
                'nat_correo' => strtolower($request->input('nat_correo')),
            ]);

            // Creación del usuario
            User::create([
                'name' => $request->input('nat_nombres'),
                'email' => $request->input('nat_correo'),
                'usu_rol' => 'ABOGADO',
                'per_id' => $persona->per_id,
                'password' => bcrypt($request->input('nat_dni')),
            ]);

            // Creación del abogado
            $abogado = Lawyer::create([
                'abo_carga_laboral' => 0,
                'abo_disponibilidad' => 'LIBRE',
                'per_id' => $persona->per_id,
            ]);

            DB::commit();

            // Recarga los modelos para obtener los datos actualizados de la base de datos
            $persona = $persona->fresh();
            $abogado = $abogado->fresh();

            // Combina los datos de persona y abogado en un solo objeto JSON
            $jsonData = array_merge($persona->toArray(), $abogado->toArray());

            return response()->json([
                'state' => 0,
                'message' => 'Abogado registrado exitosamente',
                'data' => $jsonData
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => 1,
                'message' => 'Error al registrar abogado',
                'exception' => $e->getMessage()
            ], 500);
        }
    }

    protected function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $abogado = \App\Models\Lawyer::find($request->abo_id);
            $persona = \App\Models\Person::find($abogado->per_id);
            $persona->nat_dni = trim($request->nat_dni);
            $persona->nat_apellido_paterno = strtoupper(trim($request->nat_apellido_paterno));
            $persona->nat_apellido_materno = strtoupper(trim($request->nat_apellido_materno));
            $persona->nat_nombres = strtoupper(trim($request->nat_nombres));
            $persona->nat_telefono = strtoupper(trim($request->nat_telefono));
            $persona->nat_correo = trim($request->nat_correo);
            $persona->save();
            //actulizar  su usuario 
            $user = \App\Models\User::where('per_id', $persona->per_id)->first();
            $user->name = ucwords(strtolower(trim($request->nat_nombres . ' ' . $request->nat_apellido_paterno . ' ' . $request->nat_apellido_materno)));
            $user->email = trim($request->nat_correo);
            $user->usu_rol = 'ABOGADO';
            $user->password = bcrypt(trim($request->nat_dni));
            $user->save();
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => 'actulizado correcto'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $abogado = Lawyer::findOrFail($id);
            $personaId = $abogado->per_id;

            $abogado->delete();
            User::where('per_id', $personaId)->delete();
            Person::findOrFail($personaId)->delete();

            DB::commit();

            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => 'Abogado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error al eliminar el abogado', 'exception' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    protected function expedientes(Request $request)
    {
        $procedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->where('abo_id', $request->abo_id)
            ->with('procesal.persona', 'pretension', 'materia')
            ->get();

        $formattedData = [];
        foreach ($procedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => $proceeding->exp_fecha_inicio,
                'pretencion' => $proceeding->pretension->pre_nombre,
                'materia' => $proceeding->materia->mat_nombre,
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }

        return response()->json(['data' => $formattedData], 200);
    }

    protected function alertas(Request $request)
    {
        try {
            \DB::beginTransaction();
            $today = Carbon::now('America/Lima')->startOfDay();
            $expedientes = Proceeding::where('abo_id', $request->abo_id)
                ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
                ->get();
            $alertas = collect();
            foreach ($expedientes as $expediente) {
                $alertasAbogado = $expediente->alertas()
                    ->whereDate('ale_fecha_vencimiento', '>=', $today)
                    ->get();

                foreach ($alertasAbogado as $alerta) {
                    $fechaVencimiento = Carbon::parse($alerta->ale_fecha_vencimiento);
                    $diasFaltantes = $fechaVencimiento->startOfDay()->diffInDays($today);
                    $porcentaje = round($diasFaltantes / $alerta->ale_dias_faltantes, 2);
                    $alertas->push([
                        'ale_fecha_vencimiento' => Carbon::parse($alerta->ale_fecha_vencimiento)->format('Y-m-d'), // Obtén la fecha en formato 'Y-m-d'
                        'ale_descripcion' => $alerta->ale_descripcion,
                        'fecha' => Carbon::parse($alerta->ale_fecha_vencimiento)->format('d-m-Y'),
                        'ale_expediente' => $alerta->expediente ? $alerta->expediente->exp_numero : 'N/A',
                        'ale_porcentaje' => $porcentaje,
                        'ale_exp_id'  => $alerta->expediente ? $alerta->expediente->exp_id : 'N/A',
                        'ale_id' => $alerta->ale_id
                    ]);
                }
            }
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => $alertas], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    protected function audiencias(Request $request)
    {
        try {
            \DB::beginTransaction();
            $today = Carbon::now('America/Lima')->startOfDay();
            $expedientes = Proceeding::where('abo_id', $request->abo_id)
                ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
                ->get();

            $audienciasFaltantes = collect();

            foreach ($expedientes as $expediente) {
                $audiencias = $expediente->audiencias()
                    ->whereDate('au_fecha', '>=', $today)
                    ->get();

                foreach ($audiencias as $audiencia) {
                    $fechaAudiencia = Carbon::parse($audiencia->au_fecha);
                    $diasFaltantes = $fechaAudiencia->startOfDay()->diffInDays($today);
                    $porcentaje = round($diasFaltantes / $audiencia->au_dias_faltantes, 2);
                    $audienciasFaltantes->push([
                        'au_fecha' =>  $fechaAudiencia->toDateString(),
                        'au_hora' => $audiencia->au_hora,
                        'fecha' => $audiencia->au_fecha->format('d-m-Y'),
                        'au_lugar' => $audiencia->au_lugar,
                        'au_detalles' => $audiencia->au_detalles,
                        'porcentaje' => $porcentaje,
                        'exp_id' => $audiencia->exp_id,
                        'exp_numero' => $expediente->exp_numero,
                        'id' => $audiencia->au_id,
                    ]);
                }
            }
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => $audienciasFaltantes], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function changeOfLawyer(Request $request)
    {
        try {
            $i = 0;
            $apellidoPaterno = null;
            $jur_razon_social = null;

            $expedientes = \App\Models\Proceeding::where('type_id', 1)
                ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])->get();
            foreach ($expedientes as $expediente) {
                $primerProcesal = $expediente->procesal()->orderBy('proc_id')->first();
                if ($primerProcesal->tipo_persona == 'NATURAL') {
                    $apellidoPaterno = \App\Models\Person::find($primerProcesal->persona->per_id)->nat_apellido_paterno;
                } else {
                    $jur_razon_social = \App\Models\Person::find($primerProcesal->persona->per_id)->jur_razon_social;
                }
                $letrasSeleccionadas = $request->letras_selecionadas;

                foreach ($letrasSeleccionadas as $letra) {
                    if (
                        (strtoupper(substr($apellidoPaterno, 0, 1)) == strtoupper($letra)) ||
                        (strtoupper(substr($jur_razon_social, 0, 1)) == strtoupper($letra))
                    ) {
                        $expediente->abo_id = $request->abogado_asignado;
                        $expediente->save();
                        $i++;
                    }
                }
            }
            \DB::beginTransaction();
            \App\Models\Audit::create([
                'accion' => 'Cambio de abogado a exp. con letra ',
                'model' => '\App\Models\Lawyer',
                'model_id' => $request->abogado_asignado,
                'user_id' => \Auth::user()->id,
            ]);
            \DB::commit();

            return response()->json(['cantidad' => $i, 'state' => 0], 200);
        } catch (\Exception $e) {
            // Manejar cualquier error
            return response()->json(['error' => $e->getMessage(), 'state' => 1], 500);
        }
    }

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
                    'razon_social' => ucwords(strtolower($procesalItem->persona->jur_razon_social)),
                    'telefono' => $procesalItem->persona->jur_telefono,
                    'correo' => strtolower($procesalItem->persona->jur_correo),
                    'condicion' => strtolower($procesalItem->persona->per_condicion),
                ]);
            }

            $processedProcesals[] = $data;
        }

        return $processedProcesals;
    }
    public function calendario(Request $request)
    {
        $alertas = \App\Models\Alert::obtenerAlertasFaltantesabo($request->abo_id);
        $audiences = \App\Models\Audience::obtenerAudienciasFaltantesabo($request->abo_id);
        return response()->json(['alertas' => $alertas, 'audiencias' => $audiences]);
    }

    //integrantes del equipo
    public function crearIntegrante(Request $request)
    {
        try {
            // Validaciones
            $this->validarDuplicadoDNI($request->input('nat_dni'));
            $this->validarDuplicadoCorreo(strtolower($request->input('nat_correo')));

            DB::beginTransaction();

            // Creación de la persona
            $persona = $this->crearPersona($request);

            $personaId =  $persona->getAttribute('per_id');

            // Creación del usuario
            $this->crearUsuario($request, $personaId);

            // Creación del abogado
            $this->crearAbogado($request, $personaId);

            DB::commit();

            // Respuesta exitosa
            $jsonData = $persona->fresh()->toArray();
            return response()->json(['state' => 0, 'data' => $jsonData], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['state' => 1, 'message' => $e->getMessage()], 500);
        }
    }

    private function validarDuplicadoDNI($dni)
    {
        $existingDni = Person::withTrashed()->where('nat_dni', $dni)->first();
        if ($existingDni) {
            throw new \Exception('El DNI ingresado ya existe.', 422);
        }
    }

    private function validarDuplicadoCorreo($correo)
    {
        $existingEmail = Person::withTrashed()->where('nat_correo', $correo)->first();
        if ($existingEmail) {
            throw new \Exception('Error al registrar abogado: el correo electrónico ya existe.', 422);
        }
    }

    private function crearPersona(Request $request)
    {
        return Person::create([
            'nat_dni' => $request->input('nat_dni'),
            'nat_apellido_paterno' => ucwords(strtolower($request->input('nat_apellido_paterno'))),
            'nat_apellido_materno' => ucwords(strtolower($request->input('nat_apellido_materno'))),
            'nat_nombres' => ucwords(strtolower($request->input('nat_nombres'))),
            'nat_telefono' => $request->input('nat_telefono'),
            'nat_correo' => strtolower($request->input('nat_correo')),
            'per_condicion' => $request->input('per_condicion'),
        ]);

    }

    private function crearUsuario(Request $request, $personaId)
    {
        User::create([
            'name' => $request->input('nat_nombres'),
            'email' => $request->input('nat_correo'),
            'usu_rol' => $request->input('per_condicion'),
            'per_id' => $personaId,
            'password' => bcrypt($request->input('nat_dni')),
        ]);
    }

    private function crearAbogado(Request $request, $personaId)
    {
        if ($request->input('per_condicion') === 'Abogado') {
            Lawyer::create([
                'abo_carga_laboral' => 0,
                'abo_disponibilidad' => 'LIBRE',
                'per_id' => $personaId,
            ]);
        }
    }
}
