<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;
use App\Models\PersonTrade;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $trades = Trade::with([
                'area',
                'lawyer.persona',
                'assistant.persona'
            ])->when(
                'tra_type_person',
                function ($query, $value) {
                    if ($value === 'ABOGADO') {
                        $query->where('tra_type_person', 'ABOGADO');
                    }
                    elseif ($value === 'ASISTENTE') {
                        $query->where('tra_type_person', 'ASISTENTE');
                    }
                }
            )->get();

            return response()->json(['state' => 'success', 'data' => $trades], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function create(Request $request)
    {        
        try {
            $tra_number = isset($request->tra_number) ? strtoupper(trim($request->tra_number)) : null;
            $tra_name = isset($request->tra_name) ? ucfirst(trim($request->tra_name)) : null;
            $tra_doc_recep = isset($request->tra_doc_recep) ? strtoupper(trim($request->tra_doc_recep)) : null;
            $tra_number_ext = isset($request->tra_number_ext) ? strtoupper(trim($request->tra_number_ext)) : null;
            $tra_matter = isset($request->tra_matter) ? ucfirst(trim($request->tra_matter)) : null;
            $tra_arrival_date = isset($request->tra_arrival_date) ? $request->tra_arrival_date : null;
            $tra_state_mp = isset($request->tra_state_mp) ? ucfirst($request->tra_state_mp) : null;
            $tra_state_law = isset($request->tra_state_law) ? ucfirst($request->tra_state_law) : null;
            $tra_ubication = isset($request->tra_ubication) ? strtoupper(trim($request->tra_ubication)) : null;
            $tra_are_id = isset($request->tra_are_id) ? $request->tra_are_id : null;
            $responsablesId = $request->responsablesId;
            if($request->tra_ass_id == ""){
                $tra_type_person = 'ABOGADO';
                $tra_abo_id = $request->tra_abo_id;
                $tra_ass_id = null;
            }else{
                $tra_type_person = 'ASISTENTE';
                $tra_ass_id = $request->tra_ass_id;
                $tra_abo_id = null;
            }
            $tra_pdf = isset($request->tra_pdf) ? $request->tra_pdf : null;
            
            DB::beginTransaction();
            $trade = Trade::create([
                'tra_number' => $tra_number,
                'tra_name' => $tra_name,
                'tra_number_ext' => $tra_number_ext,
                'tra_doc_recep' => $tra_doc_recep,
                'tra_matter' => $tra_matter,
                'tra_arrival_date' => $tra_arrival_date,
                'tra_state_mp' => $tra_state_mp,
                'tra_state_law' => $tra_state_law,
                'tra_ubication' => $tra_ubication,
                'tra_are_id' => $request->tra_are_id,
                'tra_type_person' => $tra_type_person,
                'tra_abo_id' => $tra_abo_id,
                'tra_ass_id' => $tra_ass_id,
                'tra_pdf' => $tra_pdf,
            ]);
            
            DB::commit();
            $content = response()->json(['state' => 'success', 'data' => $trade], 201);
            $data2 = $content->getData(true)['data'];
            if($content->getData(true)['state'] === 'success'){
                foreach ($responsablesId as $responsableId){
                    $persontrade = PersonTrade::create([
                        'pt_per_id' => $responsableId,
                        'pt_tra_id' => $data2['tra_id'],
                    ]);
                }
            }
            return $content;
        } catch (QueryException $e) {
            DB::rollback();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            error_log($errorMessage);
            echo json_encode(['error' => $errorMessage]);
            if ($errorCode == 23000) {
                return response()->json(['state' => 'error', 'message' => 'Ya existe un oficio con este numero.'], 422);
            }

            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    protected function show($id)
    {
        try {
            $trade = Trade::with([
                'area'])->find($id);
            $persons = $trade->persons;
            return response()->json(['state' => 'success', 'trade' => $trade, 'persons'=>$persons], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['state' => 'error', 'message' => 'Error de base de datos: ' . $errorMessage], 500);
        } catch (\Exception $e) {
            return response()->json(['state' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }

    }
}
