<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Http\Controllers\MailController;

class EnviarCorreoDiario extends Command
{
    protected $signature = 'correo';
    protected $description = 'Envía un correo diario';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mailController = new MailController();
        $mailController->mail();
        Log::info('Correo enviado exitosamente.');
    }
}
