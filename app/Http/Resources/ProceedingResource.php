<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProceedingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'abo_id'=> $this->abo_id,
            'exp_id'=> $this->exp_id,
            'exp_numero'=> $this->exp_numero,
            'exp_fecha_inicio'=> $this->exp_fecha_inicio,
            'exp_pretencion'=> $this->exp_pretencion,
            'exp_materia'=> $this->exp_materia,
            'exp_juzgado'=> $this->exp_juzgado,
            'exp_monto_pretencion'=> $this->exp_monto_pretencion,
            'exp_monto_ejecucion'=> $this->exp_monto_ejecucion,
            'exp_estado_proceso'=> $this->exp_estado_proceso,
            'exp_demandante'=> $this->exp_demandante,
            'exp_demandado'=> $this->exp_demandado 
        ];
}
}