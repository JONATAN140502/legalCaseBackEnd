<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Uuid;
use App\Models\Alert;
use App\Models\Audience;

class CalendarioController extends Controller
{   public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request) {
        $alertas = Alert::obtenerAlertasFaltantes();
        $audiences = Audience::obtenerAudienciasFaltantes();
        return response()->json(['alertas' => $alertas,'audiencias'=>$audiences]);
    }
   }