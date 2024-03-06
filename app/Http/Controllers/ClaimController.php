<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Illuminate\Http\Request;

class ClaimController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $pretensiones = Claim::orderBy('created_at', 'DESC')->get();
        
        return response()->json(['data' => $pretensiones], 200);
    }
    protected function show(Request $request)
    {
        $claim = \App\Models\Claim::where('pre_id', $request->pre_id)->first();
        return \response()->json(['data' => $claim], 200);
    }

    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();
           $claim = \App\Models\Claim::create([
                'pre_nombre' => strtoupper(trim($request->pre_nombre)),
                'type_id'=>$request->tipo
            ]);
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => $claim], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    protected function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $claim = \App\Models\Claim::find($request->pre_id);
            $claim->pre_nombre = strtoupper(trim($request->pre_nombre));
            $claim->save();
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

            $claim= \App\Models\Claim::find($request->pre_id);
            $claim->delete();
            \DB::commit();

            return \response()->json(['state'=>0,'data' => 'eliminado'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return \response()->json(['message' => 'Error al eliminar ', 'exception' => $e->getMessage()], 500);
        }
    }
}
