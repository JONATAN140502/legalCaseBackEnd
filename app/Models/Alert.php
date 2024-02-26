<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alert extends Model
{ 
    use HasFactory,SoftDeletes;
    protected $table = 'alerts';
    protected $primaryKey = 'ale_id';
    protected $fillable = [
        'ale_fecha_vencimiento',
        'ale_descripcion',
        'exp_id',
        'ale_dias_faltantes',
    ];
    protected $dates = ['ale_fecha_vencimiento', 'created_at', 'updated_at', 'deleted_at'];
    public function expediente()
    {
        return $this->belongsTo(Proceeding::class, 'exp_id', 'exp_id');
    }
   
    public static function obtenerAlertasFaltantes()
    {
        $today = Carbon::now('America/Lima')->startOfDay();
    
        $alertas = Alert::whereDate('ale_fecha_vencimiento', '>=', $today)
            ->select('ale_id','ale_fecha_vencimiento', 'ale_descripcion', 'exp_id', 'ale_dias_faltantes')
            ->get();
            $alertasConPorcentaje = $alertas->map(function ($alerta) use ($today) {
                $fechaVencimiento = Carbon::parse($alerta->ale_fecha_vencimiento);
                $diasFaltantes = $fechaVencimiento->startOfDay()->diffInDays($today);
                $porcentaje = round($diasFaltantes / $alerta->ale_dias_faltantes, 2);
    
            return [
                'ale_fecha_vencimiento' => $fechaVencimiento->toDateString(), // Obtén la fecha en formato 'Y-m-d'
                'ale_descripcion' => $alerta->ale_descripcion,
                'fecha' => $fechaVencimiento->format('d-m-Y'),
                'ale_expediente' => $alerta->expediente ? $alerta->expediente->exp_numero : 'N/A',
                'ale_porcentaje' => $porcentaje,
                'ale_exp_id'  => $alerta->expediente ? $alerta->expediente->exp_id : 'N/A',
                'id'=>$alerta->ale_id
            ];
        });
    
        return $alertasConPorcentaje;
    }
    public static function obtenerAlertasFaltantesabo($id)
    {
        $today = Carbon::now('America/Lima')->startOfDay();
    
        $alertas = Alert::whereDate('ale_fecha_vencimiento', '>=', $today)
            ->select('ale_id','ale_fecha_vencimiento', 'ale_descripcion', 'exp_id', 'ale_dias_faltantes')
            ->get();
            $alertasConPorcentaje = $alertas->map(function ($alerta) use ($today) {
                $fechaVencimiento = Carbon::parse($alerta->ale_fecha_vencimiento);
                $diasFaltantes = $fechaVencimiento->startOfDay()->diffInDays($today);
                $porcentaje = round($diasFaltantes / $alerta->ale_dias_faltantes, 2);
    
            return [
                'ale_fecha_vencimiento' => $fechaVencimiento->toDateString(), // Obtén la fecha en formato 'Y-m-d'
                'ale_descripcion' => $alerta->ale_descripcion,
                'fecha' => $fechaVencimiento->format('d-m-Y'),
                'ale_expediente' => $alerta->expediente ? $alerta->expediente->exp_numero : 'N/A',
                'ale_porcentaje' => $porcentaje,
                'ale_exp_id'  => $alerta->expediente ? $alerta->expediente->exp_id : 'N/A',
                'id'=>$alerta->ale_id
            ];
        });
    
        return $alertasConPorcentaje;
    }

}
