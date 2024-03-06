<?php

namespace App\Http\Controllers;

use App\Models\ProceedingTypes;
use Illuminate\Http\Request;

class ProceedingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proceeding_types = ProceedingTypes::select('id', 'name')->get();
        return response()->json($proceeding_types);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
