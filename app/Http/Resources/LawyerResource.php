<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LawyerResource extends JsonResource
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
            'nat_correo' => $this->getPersonaAttribute('nat_correo'),
            'abo_id' => $this->abo_id,
            'abo_carga_laboral' => $this->abo_carga_laboral,
            'abo_disponibilidad' => $this->abo_disponibilidad,
            'per_id' => $this->getPersonaAttribute('per_id'),
            'nat_dni' => $this->getPersonaAttribute('nat_dni'),
            'nat_apellido_paterno' => $this->getPersonaAttribute('nat_apellido_paterno'),
            'nat_apellido_materno' => $this->getPersonaAttribute('nat_apellido_materno'),
            'nat_nombres' => $this->getPersonaAttribute('nat_nombres'),
            'nat_telefono' => $this->getPersonaAttribute('nat_telefono'),
        ];
    }

    protected function getPersonaAttribute($attribute)
    {
        return $this->persona ? $this->persona->$attribute : null;
    }
}
