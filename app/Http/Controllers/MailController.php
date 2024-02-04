<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\envio;
use Illuminate\Support\Facades\Mail;
use App\Models\Proceeding;
use App\Models\Audience;
use App\Models\LegalDocument;
use App\Models\Lawyer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Uuid; 
class MailController extends Controller
{
    public function mail() {
        try {
            \DB::beginTransaction();
            $today = Carbon::now('America/Lima')->startOfDay();
            $expedientes = Proceeding::whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
                ->with('abogado.persona')
                ->get();
            // $alertas 
            foreach ($expedientes as $expediente) {
                $alertasAbogado = $expediente->alertas()
                    ->whereDate('ale_fecha_vencimiento', '>=', $today)
                    ->get();

                $i=1;
                foreach ($alertasAbogado as $alerta) {
                    $fechaVencimiento = Carbon::parse($alerta->ale_fecha_vencimiento);
                    $diasFaltantes = $fechaVencimiento->startOfDay()->diffInDays($today);
                    $porcentaje = round($diasFaltantes / $alerta->ale_dias_faltantes, 2);
                    if($diasFaltantes<=7){
                        $nombre = ucwords(strtolower(
                            $expediente->abogado->persona->nat_apellido_paterno . ' ' .
                            $expediente->abogado->persona->nat_apellido_materno . ' ' .
                            $expediente->abogado->persona->nat_nombres
                        ));
                        $asunto = 'Recordatorio de Alerta:';
                        $descripcion1 = 'Hola, te recordamos que tienes una alerta del Expediente ' . $expediente->exp_numero ;
                        $descripcion2 = 'Con detalle: ' . $alerta->ale_descripcion ;
                        $descripcion3 = 'Con fecha de vencimiento: ' . $fechaVencimiento->format('d-m-Y');
                        
                        $destino = $expediente->abogado->persona->nat_correo;
                        $correo = new envio($nombre, $asunto, $descripcion1,$descripcion2,$descripcion3);
                        $correo->subject('Recordatorio de Alerta-Legal Case UNPRG-'.$i);
                        Mail::to($destino)->send($correo);                    
                    }
                  $i++;  
                }
            }
            //audiencias
            foreach ($expedientes as $expediente) {
                $audiencias = $expediente->audiencias()
                    ->whereDate('au_fecha', '>=', $today)
                    ->get();
                    $i=1;
                foreach ($audiencias as $audiencia) {
                   
                    $fechaAudiencia = Carbon::parse($audiencia->au_fecha);
                    $diasFaltantes = $fechaAudiencia->startOfDay()->diffInDays($today);
                    $porcentaje = round($diasFaltantes / $audiencia->au_dias_faltantes, 2);
                        if($diasFaltantes<=7){
                            
                            $nombre = ucwords(strtolower(
                                $expediente->abogado->persona->nat_apellido_paterno . ' ' .
                                $expediente->abogado->persona->nat_apellido_materno . ' ' .
                                $expediente->abogado->persona->nat_nombres
                            ));
                            $asunto = 'Recordatorio de Audiencia:';
                            $descripcion1 = 'Hola, te recordamos que tienes una audiencia del Expediente :' . $expediente->exp_numero.',
                             a realizarse en:'.$audiencia->au_lugar;
                            $descripcion2 = 'Con detalle: ' . $audiencia->au_detalles;
                            $descripcion3 = 'El dia : ' . $fechaAudiencia->format('d-m-Y').'
                             a horas:'.$audiencia->au_hora;
                            $destino = $expediente->abogado->persona->nat_correo;
                            $correo = new envio($nombre, $asunto, $descripcion1,$descripcion2,$descripcion3);
                            $correo->subject('Recordatorio de Audiencia-Legal Case UNPRG-'.$i);
                            Mail::to($destino)->send($correo);                    
                        }
                $i++;
                }
            }
            $twoDaysAgo = Carbon::now('America/Lima')->startOfDay()->subDays(2);
            $escritos = LegalDocument::whereDate('created_at', $twoDaysAgo)
                ->where('doc_desciprcion', 'LIKE', '%sentencia%')
                ->with('proceeding.abogado.persona') 
                ->get();

            foreach ($escritos as $escrito) {
                $i=1;
                $proceeding = $escrito->proceeding;

                // Access lawyer information through the proceeding relationship
                $abogado = $proceeding->abogado;
                $nombreAbogado = ucwords(strtolower(
                    $abogado->persona->nat_apellido_paterno . ' ' .
                    $abogado->persona->nat_apellido_materno . ' ' .
                    $abogado->persona->nat_nombres
                ));

                // Check if the document is also less than 7 days old
                $daysSinceCreation = Carbon::parse($escrito->created_at)->startOfDay()->diffInDays($today);

                if ($daysSinceCreation >= 2 && $daysSinceCreation <=7) {
                    $asunto = 'Notificación de Sentencia en Escrito:';
                    $descripcion1 = 'Hola, se ha registrado una sentencia en un escrito del Expediente ' . $proceeding->exp_numero;
                    $descripcion2 = 'Detalles: ' . $escrito->doc_desciprcion;
                    $descripcion3='Importante: Puede ser Oportunidad de Apelación';
                    $destino = $abogado->persona->nat_correo;
                    $correo = new envio($nombreAbogado, $asunto, $descripcion1, $descripcion2,$descripcion3);
                    $correo->subject('Notificación de Sentencia en Escrito-Legal Case UNPRG-'.$i);
                    Mail::to($destino)->send($correo);
                }
                $i++;
            }  
        \DB::commit();
         return response()->json(['state' =>0 ,'data'=>$expedientes], 200);
        } catch (\Exception $e) {
            return response()->json(['state' => 1, 'exception' => (string)$e], 500);
        }
    }
}
