<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Uuid;

class CourtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index(Request $request)
    {
        try {
            $juzgados =\App\Models\Court::where('judis_id',$request->judis_id)
            ->orderBy('created_at', 'DESC')
            ->get(['co_id','co_nombre','judis_id','co_isFavorite','type_id']);
            return response()->json(['state' => 0, 'data' => $juzgados], 200);
        } catch (\Exception $e) {
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }


    protected function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $court = \App\Models\Court::create([
                'co_nombre' =>  strtoupper(trim($request->co_nombre)),
                'judis_id' =>  strtoupper(trim($request->judis_id)),
                'type_id'=>strtoupper(trim($request->tipo))
            ]);
            DB::commit();
            return response()->json(['state' => 0, 'data' => $court], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }

    protected function update(Request $request){
        try{
            DB::beginTransaction();
            $court = \App\Models\Court::find($request->co_id);
            $court->co_nombre = strtoupper(trim($request->co_nombre));
            $court->judis_id = strtoupper(trim($request->judis_id));
            $court->save();
            DB::commit();
            return \response()->json(['state' => 0], 200);

        }catch(Exception $e){
            DB::rollBack();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function destroy(Request $request){
        try {
            DB::beginTransaction();
            $court = \App\Models\Court::find($request->co_id);
            $court->delete();
           
            DB::commit();
            return \response()->json(['state' => 0, 200]);

        } catch (Exception $e) {
            DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }

    }
    public function favorite(Request $request)
    {
        try {
            DB::beginTransaction();
           $reset= \App\Models\Court::query()->update(['co_isFavorite' => 0]);
            $juzgadosIds = $request->favoritos;
                foreach ($juzgadosIds as $juzgado) {
                    $court = \App\Models\Court::find($juzgado);
                    if ($court) {
                        $court->co_isFavorite = 1; 
                        $court->save();
                    }
                }
                
                DB::commit();
                return response()->json(['state' => 0, 'message' => 'Actualización exitosa']);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }
    
    

}
