<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Province;
use App\Models\District;
use App\Http\Resources\DepartmentResource;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $departments = Department::orderBy('created_at', 'DESC')->get();
        $data = DepartmentResource::collection($departments);

        return response()->json(['data' => $data], 200);
    }

    public function show($id)
    {
        $department = Department::select('dep_id', 'dep_nombre')->find($id);

        return $department;
    }

    public function provincias(Request $request)
    {
        $provinces = Province::where('dep_id', $request->dep_id)
            ->orderBy('created_at', 'DESC')
            ->get(['pro_id','pro_nombre','dep_id']);

        return response()->json(['data' => $provinces], 200);
    }

    public function distritos(Request $request)
    {
        $districts = District::where('pro_id', $request->pro_id)
            ->orderBy('created_at', 'DESC')
            ->get(['dis_id','dis_nombre','pro_id']);

        return response()->json(['data' => $districts], 200);
    }
}
