<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','create','affiliatesPost']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if ($token = Auth::attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function affiliatesGet(Request $request,$uuid= null) {
        if ( isset($uuid) ) {
            $user = User::where('uuid',$uuid)->first();
            if ( !empty($user) )
                return response()->json(['afiliado'=>$user],200);
            else return response()->json(['no existe el afiliado'],204);
        }
        $users = User::all();
        if ( !empty($users) )
            return response()->json(['afiliados'=>$users],200);
        else
            return response()->json('no existen afiliados',204);

        return response()->json('Error',500);
    }

    public function create(Request $request) {

        $name = $request->input("name");
        $email = $request->input("email");
        $password = $request->input("password");

        if(!empty($name) && !empty($email) && !empty($password)) {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $valiEmail = User::where('email', $email)->first();
            if(!empty($valiEmail['email'])) {
               return view('register', [
                    "information"=> "email exist"
                   ]);
            }
            $user->password = bcrypt($password);
            $user->save();
            return response()->json(['msg' => 'usuario registrado']);
        }
        return  view('register', [
            "information"=> "Te falta algun campo por rellenar"
        ]);
    }


    public function affiliatesPost(Request $request) {

        $nombre = $request->input("nombre");
        $apellidos = $request->input("apellidos");
        $email = $request->input("email");
        $password = $request->input("password");

        if( !empty($nombre) && !empty($email) && !empty($apellidos) && !empty($password)) {
            $user = new User();
            $user->name = $nombre;
            $user->apellidos = $apellidos;
            $user->email = $email;

            $valiEmail = User::where('email', $email)->first();
            $uuid=Str::uuid();
            $user->uuid = $uuid;
            if( !empty($valiEmail['email']) ) {
              // return view('register', ["information"=> "email exist"]);
              return response()->json(['msg' => 'usuario existe'],409);
            }
            $user->password = bcrypt($password);

            $user->save();
            //$id = User::select('id')->where('email', $email)->first();
            //return response()->json(['uuid' => $user->uuid],200);
            return response()->json(['uuid' => $uuid],200);
        }
        //return  view('register', ["information"=> "faltan campos por rellenar"]);
        return response()->json(['msg' => 'error en la introducción de datos'],500);
    }
}
