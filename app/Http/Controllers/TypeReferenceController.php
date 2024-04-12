<?php

namespace App\Http\Controllers;

use App\Models\TypeReference;
use Illuminate\Http\Request;

class TypeReferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index(){
        $types = TypeReference::all();
        $formattedData = [];
        foreach ($types as $type) {
            $commonData = [
                'type_id' => $type->type_id,
                'type_name' => $type->type_name,
            ];
            $formattedData[] = $commonData;
        }
        
        return response()->json(['data' => $formattedData], 200);
    }
}
