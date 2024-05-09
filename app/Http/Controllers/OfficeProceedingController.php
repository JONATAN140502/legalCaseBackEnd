<?php

namespace App\Http\Controllers;

use App\Models\OfficeProceeding;
use Illuminate\Http\Request;

class OfficeProceedingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getOfficeProceeding()
    {
        return response()->json(OfficeProceeding::all(), 200);
    }

    public function getOfficeProceedingxid($id)
    {
        $oproceeding = OfficeProceeding::find($id);
        if (is_null($oproceeding)) {
            return response()->json(['Mensaje' => 'No encontrado'], 404);
        }
        return response()->json($oproceeding::find($id), 200);
    }

    public function insertOfficeProceeding(Request $request)
    {
        // Obtener el año actual
        $currentYear = date('Y');

        // Obtener el último registro
        $lastRecord = OfficeProceeding::latest()->first();

        // Obtener el año del último registro
        $lastRecordYear = $lastRecord ? date('Y', strtotime($lastRecord->created_at)) : null;

        // Verificar si el año del último registro es diferente al año actual
        if ($lastRecordYear != $currentYear) {
            // Si es diferente, reiniciar el contador
            $newId = 1;
        } else {
            // Obtener el último número correlativo para el año actual
            $lastId = OfficeProceeding::whereYear('created_at', $currentYear)->max('id');

            // Incrementar el último número correlativo
            $newId = $lastId ? $lastId + 1 : 1;
        }

        // Formatear el nuevo número correlativo
        $formattedId = sprintf('%04d', $newId);

        // Construir el número de correlativo
        $nCorrelativo = "OAJ-$currentYear-$formattedId";

        // Agregar el número correlativo al request
        $request->merge(['n_correlativo' => $nCorrelativo]);

        // Crear el oficio
        $oproceeding = OfficeProceeding::create($request->all());

        return response()->json($oproceeding, 201);
    }

    public function updateOfficeProceeding(Request $request, $id)
    {
        $oproceeding = OfficeProceeding::find($id);
        if (is_null($oproceeding)) {
            return response()->json(['Mensaje' => 'No encontrado'], 404);
        }
        $oproceeding->update($request->all());
        return response($oproceeding, 200);
    }

    public function deleteOfficeProceeding(Request $request, $id)
    {
        $oproceeding = OfficeProceeding::find($id);
        if (is_null($oproceeding)) {
            return response()->json(['Mensaje' => 'No encontrado'], 404);
        }
        $oproceeding->delete();
        return response()->json(['Mensaje' => 'Eliminado Correctamente'], 200);
    }
}
