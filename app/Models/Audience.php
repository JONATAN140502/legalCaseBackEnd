<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audience extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'au_id';
    protected $fillable = [
        'au_fecha',
        'au_hora',
        'au_lugar',
        'au_link',
        'au_detalles',
        'au_dias_faltantes',
        'exp_id',
        'per_id',
        'abo_id'
    ];
    protected $dates = [ 'au_fecha','deleted_at'];
    public function exp()
    {
        return $this->belongsTo(Proceeding::class, 'exp_id', 'exp_id');
    }
    public function person()
    {
        return $this->belongsTo(Person::class, 'per_id', 'per_id');
    }
    public function expediente()
    {
        return $this->belongsTo(Proceeding::class, 'exp_id', 'exp_id');
    }
    public static function obtenerAudienciasFaltantes()
    {
        $today = Carbon::now('America/Lima')->startOfDay();

        $audiencias = self::whereDate('au_fecha', '>=', $today)
            ->whereNotNull('au_hora')
            ->whereNotNull('au_lugar')
            ->get();

        $audienciasConPorcentaje = $audiencias->map(function ($audiencia) use ($today) {
            $fechaAudiencia = Carbon::parse($audiencia->au_fecha);
            $diasFaltantes = $fechaAudiencia->startOfDay()->diffInDays($today);
            $porcentaje = round($diasFaltantes / $audiencia->au_dias_faltantes, 2);

            return [
                'au_fecha' => $fechaAudiencia->toDateString(),
                'au_hora' => $audiencia->au_hora,
                'fecha' =>  $fechaAudiencia->format('d-m-Y'),
                'au_lugar' => $audiencia->au_lugar,
                'au_detalles' => $audiencia->au_detalles,
                'porcentaje' => $porcentaje,
                'exp_id' => $audiencia->exp_id,
                'exp_numero' => $audiencia->expediente->exp_numero,
                'id'=>$audiencia->au_id,
            ];
        });

        return $audienciasConPorcentaje;
    }
    public static function obtenerAudienciasFaltantesabo($id)
    {
        $today = Carbon::now('America/Lima')->startOfDay();

        $audiencias = self::whereDate('au_fecha', '>=', $today)
            ->whereNotNull('au_hora')
            ->whereNotNull('au_lugar')
            ->where('abo_id',$id)
            ->get();

        $audienciasConPorcentaje = $audiencias->map(function ($audiencia) use ($today) {
            $fechaAudiencia = Carbon::parse($audiencia->au_fecha);
            $diasFaltantes = $fechaAudiencia->startOfDay()->diffInDays($today);
            $porcentaje = round($diasFaltantes / $audiencia->au_dias_faltantes, 2);

            return [
                'au_fecha' => $fechaAudiencia->toDateString(),
                'au_hora' => $audiencia->au_hora,
                'fecha' =>  $fechaAudiencia->format('d-m-Y'),
                'au_lugar' => $audiencia->au_lugar,
                'au_detalles' => $audiencia->au_detalles,
                'porcentaje' => $porcentaje,
                'exp_id' => $audiencia->exp_id,
                'exp_numero' => $audiencia->expediente->exp_numero,
                'id'=>$audiencia->au_id,
            ];
        });

        return $audienciasConPorcentaje;
    }
}
