<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
public function convertirMinusculasTildadasAMayusculas($cadena) {
        $minusculas = ['á', 'é', 'í', 'ó', 'ú', 'ü'];
        $mayusculas = ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü'];
        return str_replace($minusculas, $mayusculas, mb_strtoupper($cadena, 'UTF-8'));
}
public function index(Request $request)
{
    // try {
    //     \DB::beginTransaction();

        $file = $request->file('excel');
        $data = Excel::toArray([], $file)[0];
        $notInsertedRows = [];
        for ($i = 1; $i < count($data); $i++) {
            try {
            $row = $data[$i];

            // if(empty($row[5]) && empty($row[8])){
            //     $notInsertedRows[] = $i.'=juzgado';
            //     continue; 
            // }
            
            if (\App\Models\Proceeding::where('exp_numero',strtoupper(trim($row[0])))->exists()) {
                $notInsertedRows[] = $i.'expedienteexiste';
                continue; 
            }
            if(empty($row[11]) && empty($row[15])){
                $notInsertedRows[] = $i.'ruc-dni';
                continue; 
            }
             $nombreabogado=trim($row[23]);
             $user=\App\Models\User::
             where('name',$nombreabogado)->first();

             if ($user === null || empty($user)) {
                $notInsertedRows[] = $i.'=no abogado';
                continue;
             }
            $abogado=\App\Models\Lawyer::
            where('per_id',$user->per_id)->first();
            \DB::beginTransaction();
             $nombredistrito=trim($row[5]);
             $distrito = \App\Models\JudicialDistrict::where('judis_nombre', $nombredistrito)->first();

             $nombreinstancia=(trim($row[6]));
             $instancia = \App\Models\Instance::

             where('ins_nombre',$nombreinstancia)->first();
             $nombreespecialidad=trim($row[7]);
             $especialidad = \App\Models\Specialty::
             where('esp_nombre',$nombreespecialidad)->first();

             $nombrejuzgado=trim($row[8]);
             $juzgado= \App\Models\Court::where('judis_id',$distrito->judis_id)
             ->where('co_nombre',$nombrejuzgado)->first();
            //  if ($juzgado === null || empty($juzgado)) {
            //     $notInsertedRows[] = $i.'=juszgado-distritosi';
            //     continue;
            // }
             $nombrepre=trim($row[2]);
             $pretension= \App\Models\Claim::where('pre_nombre',$nombrepre)
             ->first();

             $nombremateria=trim($row[4]);
             $materia= \App\Models\Subject::
             where('mat_nombre',$nombremateria)->first();
             //
            
            $exp = \App\Models\Proceeding::create([
                'exp_numero' => strtoupper(trim($row[0])),
                'exp_fecha_inicio' => Carbon::parse($row[1])->format('Y-m-d'),
                'exp_materia' =>$materia ?$materia->mat_id:null,
                'exp_pretencion' =>$pretension ? $pretension->pre_id : null,
                'exp_monto_pretencion' => strtoupper(trim($row[3])) ? strtoupper(trim($row[3])):null,
                'exp_estado_proceso' =>$row[24]?strtoupper(trim($row[24])):'EN TRAMITE',
                'exp_especialidad' =>$especialidad ? $especialidad->esp_id:null,
                'exp_dis_judicial' =>$distrito ? $distrito->judis_id:null,
                'exp_instancia' =>$instancia? $instancia->ins_id :null,
                'exp_juzgado' =>$juzgado?$juzgado->co_id:null,
                'multiple' =>0,
                'abo_id' =>$abogado? $abogado->abo_id:null
            ]);
            $persona = null;
            $direccion = null;
            $procesal = null;
            $tipo=null;
            // Verificar si la persona ya existe
            if ($row[11]!=null) {
                $persona = \App\Models\Person::updateOrCreate(
                    ['nat_dni' =>trim($row[11])],
                    [
                        'nat_apellido_paterno' =>  strtoupper(trim($row[12])),
                        'nat_apellido_materno' =>  strtoupper(trim($row[13])),
                        'nat_nombres' =>  strtoupper(trim($row[14])),
                        'nat_telefono' =>  strtoupper(trim($row[17])),
                        'nat_correo' => trim($row[18])==null ? trim($row[11]):trim($row[18]),
                        'per_condicion'=>strtoupper(trim($row[10])),
                        'tipo_procesal'=> strtoupper(trim($row[9])),
                    ]
                );
                 $tipo='NATURAL';
            } else {
                $persona = \App\Models\Person::updateOrCreate(
                    ['jur_ruc' => strtoupper(trim($row[15]))],
                    [
                        'jur_razon_social' => strtoupper(trim($row[16])),
                        'jur_telefono' =>  strtoupper(trim($row[17])),
                        'jur_correo' =>  trim($row[18])==null ? trim($row[15]):trim($row[18]),
                        'jur_rep_legal' => '-',
                        'per_condicion'=>strtoupper(trim($row[10])),
                        'tipo_procesal'=> strtoupper(trim($row[9])),
                    ]
                );
                $tipo='JURIDICA';
            }
            
            $procesal = \App\Models\Procesal::Create(
                [
                    'per_id'=> $persona->per_id,
                    'exp_id'=>$exp->exp_id,
                    'tipo_procesal'=> strtoupper(trim($row[9])),
                    'tipo_persona'=>$tipo
                ]
            );
            $nombredepartamento = trim($row[19]);
            $departamento = \App\Models\Department::where('dep_nombre', $nombredepartamento)->first();
            $nombreprovincia = trim($row[20]);
            $provincia = null;
            if ($departamento) {
                $provincia = \App\Models\Province::where('dep_id', $departamento->dep_id)
                    ->where('pro_nombre', $nombreprovincia)->first();
            }
            $nombredistrito1 = trim($row[21]);
            if ($nombredistrito1 == 'JLO') {
                $nombredistrito1 = 'José Leonardo Ortiz';
            }
            $distrito = null;
            if ($provincia) {
                $distrito = \App\Models\District::where('pro_id', $provincia->pro_id)
                    ->where('dis_nombre', $nombredistrito1)->first();
            }
            $direccion = \App\Models\Address::updateOrCreate(
                ['per_id' => $persona->per_id],
                [
                    'dir_calle_av' => trim($row[22]),
                    'dis_id' => $distrito ? $distrito->dis_id : null,
                    'pro_id' => $provincia ? $provincia->pro_id : null,
                    'dep_id' => $departamento ? $departamento->dep_id : null,
                ]
            );
             $abogado = \App\Models\Lawyer::find($abogado ->abo_id);
             $abogado->abo_disponibilidad = 'OCUPADO';
             $abogado->abo_carga_laboral = $abogado->abo_carga_laboral + 1;
             $abogado->save();
             \DB::commit();
         } catch (Exception $e) {
             \DB::rollback(); 
             $notInsertedRows[] = $i.'=error='.$e;
               
         }
        }
    return response()->json(['state' => 0, 'data' => $notInsertedRows], 200);
    // return response()->json(['state' => 0, 'data' => $data], 200);
}

}