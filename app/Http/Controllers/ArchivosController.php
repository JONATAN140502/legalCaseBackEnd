<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Uuid;
class ArchivosController extends Controller
{   public function __construct()
    {
        $this->middleware('auth');
    }
    public function pdfprincipal(Request $request) {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
    
        if ($request->doc_tipo == 'EJE') {
            $filePath = $file->storeAs('public/files/ejes', $fileName);
        } else {
            $filePath = $file->storeAs('public/files/escritos', $fileName);
        }
    
        // Guardar datos en la base de datos
        $document = \App\Models\LegalDocument::create([
            'doc_nombre' => $file->getClientOriginalName(),
            'doc_tipo' => $request->doc_tipo,
            'doc_desciprcion' => $request->descripcion,
            'doc_ruta_archivo' => ($request->doc_tipo == 'EJE') ? 'public/files/ejes/' . $fileName : 'public/files/escritos/' . $fileName,
            'exp_id' => $request->exp_id
        ]);
    
        return response()->json(['message' => 'Archivo cargado con éxito', 'file' => $fileName]);
    }
    public function traerpdfprincipal(Request $request) {
        $rutaArchivo = storage_path('app/'.$request->nombre);
        return response()->download($rutaArchivo);
    }
    public function eje(Request $request) {
        Storage::delete($request->name);
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('public/files/ejes', $fileName);
        $document = \App\Models\LegalDocument::where('exp_id',$request->exp_id)
        ->where('doc_tipo','EJE')->first();
        $document->doc_nombre =$file->getClientOriginalName();
        $document->doc_desciprcion =$request->descripcion;
        $document->doc_ruta_archivo = 'public/files/ejes/' . $fileName ;
        $document->exp_id = $request->exp_id;
       $document->save();
        return response()->json(['message' => 'Archivo cargado con éxito', 'file' => $fileName]);
    }
}
