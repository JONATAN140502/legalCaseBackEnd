<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\JudicialDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class JudicialDistrictController extends Controller
{
    public function index()
    {
        try {
            $districts = JudicialDistrict::latest()->get();
            return response()->json($districts, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judis_nombre' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $district = JudicialDistrict::create($request->all());

            DB::commit();

            return response()->json($district, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $district = JudicialDistrict::findOrFail($id);
            return response()->json($district, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'District not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judis_nombre' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $district = JudicialDistrict::findOrFail($id);
            $district->update($request->all());

            DB::commit();

            return response()->json($district, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $district = JudicialDistrict::findOrFail($id);
            $district->delete();

            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
