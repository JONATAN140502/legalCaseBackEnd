<?php

namespace App\Http\Controllers;

use App\Models\LegalDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ArchivosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function descargar(Request $request)
    {
        // try {
        //     $nombreArchivo = $request->nombre;
        //     $rutaArchivo = storage_path("app/{$nombreArchivo}");
        //     if (!Storage::exists($nombreArchivo)) {
        //         throw new \Exception('Archivo no encontrado');
        //     }
        //     return response()->download($rutaArchivo);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Error al descargar el archivo: ' . $e->getMessage()], 500);
        // }
        try {
            $nombreArchivo = $request->nombre;
            $rutaArchivo = Storage::disk('public_server')->path($nombreArchivo);
            if (!Storage::disk('public_server')->exists($nombreArchivo)) {
                throw new \Exception('Archivo no encontrado');
            }
            return response()->download($rutaArchivo);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al descargar el archivo: ' . $e->getMessage()], 500);
        }
    }
    public function guardar(Request $request)
    {
        try {
            //STORAGE
            // $archivo = $request->file('file');
            // /$nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
             $directorio = ($request->doc_tipo=='EJE') ? 'ejes' : 'escritos';
            // $archivo->storeAs("public/files/{$directorio}", $nombreArchivo);
            //DISCO
            $archivo = $request->file('file');
            $original= $archivo->getClientOriginalName();
            $uuid_file = \Illuminate\Support\Str::uuid();
            $extension = $archivo->getClientOriginalExtension();
            $doc_file = "{$directorio}/{$uuid_file}.{$extension}";
    
            // Almacena el archivo en el disco 'public_server'
            Storage::disk('public_server')->put($doc_file, file_get_contents($archivo));

            // Guardar datos en la base de datos
            $documento = LegalDocument::create([
                'doc_nombre' => $original,
                'doc_tipo' => $request->doc_tipo,
                'doc_desciprcion' => $request->descripcion,
                // 'doc_ruta_archivo' => "public/files/{$directorio}/{$nombreArchivo}",
                'doc_ruta_archivo' =>$doc_file,
                'exp_id' => $request->exp_id,
            ]);

            // Recupera los datos recién almacenados en la base de datos
            $nuevosDatos = LegalDocument::find($documento->doc_id);

            // Devuelve los datos recién almacenados en caso de éxito
            return response()->json([
                'mensaje' => 'Archivo cargado exitosamente',
                'data' => $nuevosDatos
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar el archivo: ' . $e->getMessage()], 500);
        }
    }
    public function actualizarEje(Request $request)
    {
        try {
            // Elimina el archivo existente
            Storage::disk('public_server')->delete($request->name);

            // Almacena el nuevo archivo
            // $file = $request->file('file');
            // $fileName = time() . '_' . $file->getClientOriginalName();
            // $file->storeAs('public/files/ejes', $fileName)
            $archivo = $request->file('file');
            $original= $archivo->getClientOriginalName();
            $uuid_file = \Illuminate\Support\Str::uuid();
            $extension = $archivo->getClientOriginalExtension();
            $doc_file = "ejes/{$uuid_file}.{$extension}";
            Storage::disk('public_server')->put($doc_file, file_get_contents($archivo));

            // Actualiza los detalles del documento en la base de datos
            LegalDocument::updateOrCreate(
                ['exp_id' => $request->exp_id, 'doc_tipo' => 'EJE'],
                [
                    'doc_nombre' => $original,
                    'doc_desciprcion' => $request->descripcion,
                    'doc_ruta_archivo' => $doc_file,
                    'exp_id' => $request->exp_id,
                ]
            );

            return response()->json(['message' => 'Archivo actualizado con éxito', 'file' => $original]);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json(['error' => 'Error al actualizar el archivo: ' . $e->getMessage()], 500);
        }
    }
}
