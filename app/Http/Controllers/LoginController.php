<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\{
    PermissionResource
};

class LoginController extends Controller
{
    protected function credentials(Request $request)
    {
        $login = strtoupper($request->input($this->username()));
        $field = 'email';
        return [
            $field     => $login,
            'password' => $request->input('password')
        ];
    }
    public function username()
    {
        return 'login';
    }
    
    public function login(Request $request)
    {
        $credentials = $this->credentials($request);
        if (!Auth::attempt($credentials)) {
            return response()->json(["message" => "Usuario y/o contraseña es inválido",
             "state" => 401], 200);
        }
    
        $user = Auth::user();
        $accessToken = $user->createToken('authTestToken')->accessToken;
    
        // Acceder a la persona natural asociada al usuario
        $personaNatural = $user->personaNatural;
    
        if ($personaNatural) {
            $datosPersonaNatural = [
                'dni' => $personaNatural->nat_dni,
                'apellido_paterno' => $personaNatural->nat_apellido_paterno,
                'apellido_materno' => $personaNatural->nat_apellido_materno,
                'nombres' => $personaNatural->nat_nombres,
                'telefono' => $personaNatural->nat_telefono,
                'correo' => $personaNatural->nat_correo,
            ];
        if($user->usu_rol=='ABOGADO'){
            $abogado = \App\Models\Lawyer::where(
                'per_id',$personaNatural->per_id)->first();
        }
        
        \App\Models\Audit::create([
            'accion'=>'Ingreso Al Sistema',
           'model'=>'\App\Models\User',
            'model_id'=>Auth::user()->id,
            'user_id'=>Auth::user()->id,
        ]);
        
        return response()->json([
                'user' => [
                    'id' => $user->id,
                    'usu_rol'=>$user->usu_rol,
                    'name' =>   ucwords(strtolower($personaNatural->nat_apellido_paterno)).' '.
                     ucwords(strtolower($personaNatural->nat_apellido_materno)).' '.
                     ucwords(strtolower($personaNatural->nat_nombres)),
                    'email' => $user->email,
                    'token' => $accessToken,
                    'datos' => $datosPersonaNatural,
                    'abo_id'=>$user->usu_rol=='ABOGADO' ?$abogado->abo_id:null,
                ],
                'state' => 200
            ], 200);
        }
    
        return response()->json(["message" => "Error al obtener datos de la persona", "state" => 500], 500);
    }
}
