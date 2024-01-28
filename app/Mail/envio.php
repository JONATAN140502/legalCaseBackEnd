<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class envio extends Mailable
{
    
        use Queueable, SerializesModels;
    
        public $nombre;
        public $asunto;
        public $descripcion;
    
        public function __construct($nombre, $asunto, $descripcion1,$descripcion2,$descripcion3)
        {
            $this->nombre = $nombre;
            $this->asunto = $asunto;
            $this->descripcion1 = $descripcion1;
            $this->descripcion2 = $descripcion2;
            $this->descripcion3 = $descripcion3;
        }
    
        public function build()
        {
            return $this->view('emails.mail')
                        ->with([
                            'nombre' => $this->nombre,
                            'asunto' => $this->asunto,
                            'descripcion1' => $this->descripcion1,
                            'descripcion2' => $this->descripcion2,
                            'descripcion3' => $this->descripcion3,
                        ]);
        }
     
}
