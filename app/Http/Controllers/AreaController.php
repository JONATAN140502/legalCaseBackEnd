<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index(){
        $areas = Area::all();
        $formattedData = [];
        foreach ($areas as $area) {
            $commonData = [
                'are_id' => $area->are_id,
                'are_name' => $area->are_name,
                'are_email' => $area->are_email,
            ];
            $formattedData[] = $commonData;
        }
        
        return response()->json(['data' => $formattedData], 200);
    }
}
