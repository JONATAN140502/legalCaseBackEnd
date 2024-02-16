<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// app/Http/Controllers/OficioController.php

use App\Models\Trade;

class TradeController extends Controller
{
    public function index()
    {
        $oficios = Trade::pluck('tra_matter', 'id'); // Utiliza 'tra_matter' como nombre y 'id' como valor
        return response()->json($oficios);
    }
}
