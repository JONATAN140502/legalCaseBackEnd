<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Obtener todos los datos
    protected function index(Request $request)
    {
        $subjects = \App\Models\Subject::orderBy('created_at', 'DESC')->get();
        return \response()->json(['data' => $subjects], 200);
    }
    protected function show(Request $request)
    {
        $subject = \App\Models\Subject::where('mat_id', $request->mat_id)->first();
        return \response()->json(['data' => $subject], 200);
    }

    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();
           $subject = \App\Models\Subject::create([
                'mat_nombre' => strtoupper(trim($request->mat_nombre)),
            ]);
           
            \App\Models\Audit::create([
              'accion'=>'Registro de Materia',
             'model'=>'\App\Models\Subject',
              'model_id'=>$subject->mat_id,
              'user_id'=>\Auth::user()->id,
          ]);
           
     
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => $subject], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    protected function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $subject = \App\Models\Subject::find($request->mat_id);
            $subject->mat_nombre = strtoupper(trim($request->mat_nombre));
            $subject->save();
            \App\Models\Audit::create([
                'accion'=>'Edición de Materia',
               'model'=>'\App\Models\Subject',
                'model_id'=>$subject->mat_id,
                'user_id'=>\Auth::user()->id,
            ]);
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => 'actulizado correcto'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function eliminar(Request $request)
    {
        try {
            \DB::beginTransaction();

            $subject = \App\Models\Subject::find($request->mat_id);
            $subject->delete();
            \App\Models\Audit::create([
                'accion'=>'Eliminación de Materia',
               'model'=>'\App\Models\Subject',
                'model_id'=>$subject->mat_id,
                'user_id'=>\Auth::user()->id,
            ]);
            \DB::commit();

            return \response()->json(['state'=>0,'data' => 'eliminado'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return \response()->json(['message' => 'Error al eliminar ', 'exception' => $e->getMessage()], 500);
        }
    }
}
