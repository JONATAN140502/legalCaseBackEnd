<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Proceeding;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProceedingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $proceeding = Proceeding::latest()
                ->with('procesal.persona', 'materia', 'pretension', 'montos')
                ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
                ->get();

            return response()->json($proceeding, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
