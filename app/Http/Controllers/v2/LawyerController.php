<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use App\Models\Person;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class LawyerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $lawyers = Lawyer::with('persona')->latest()->get();
            return response()->json($lawyers, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nat_dni' => 'required|unique:persons,nat_dni',
            'nat_correo' => 'required|email|unique:persons,nat_correo',
            'nat_apellido_paterno' => 'required|string',
            'nat_apellido_materno' => 'nullable|string',
            'nat_nombres' => 'required|string',
            'nat_telefono' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $persona = Person::create($request->all());

            $user['name'] = $request->nat_nombres;
            $user['email'] = $request->nat_correo;
            $user['usu_rol'] = 'ABOGADO';
            $user['per_id'] = $persona->per_id;
            $user['password'] = bcrypt($request->nat_dni);
            User::create($user);

            $lawyer['per_id'] = $persona->per_id;
            $lawyer = Lawyer::create($lawyer);
            $lawyer->load('persona');

            DB::commit();
            return response()->json($lawyer, Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['exception' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $lawyer = Lawyer::findOrFail($id);
            $lawyer->load('persona');
            return response()->json($lawyer, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lawyer Nout Found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lawyer = Lawyer::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nat_dni' => 'required|unique:persons,nat_dni,' . $lawyer->per_id . ',per_id',
            'nat_correo' => 'required|email|unique:persons,nat_correo,' . $lawyer->per_id . ',per_id',
            'nat_apellido_paterno' => 'required|string',
            'nat_apellido_materno' => 'nullable|string',
            'nat_nombres' => 'required|string',
            'nat_telefono' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $lawyer->persona->update($request->all());

            $user = User::where('per_id', $lawyer->persona->per_id)->firstOrFail();
            $user['name'] = $request->nat_nombres;
            $user['email'] = $request->nat_correo;
            $user->save();

            $lawyer->load('persona');

            DB::commit();

            return response()->json($lawyer, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lawyer Nout Found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $lawyer = Lawyer::findOrFail($id);
            $lawyer->delete();
            $lawyer->persona->delete();
            $user = User::where('per_id', $lawyer->persona->per_id)->firstOrFail();
            $user->delete();

            DB::commit();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lawyer Nout Found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
