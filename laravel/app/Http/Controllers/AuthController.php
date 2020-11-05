<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
use Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    protected $loginValidation;

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $validation = $this->loginValidation($request);

        if ($validation) {
            $credentials['password'] = $request->password;
            $credentials['email'] = $request->email;
        } else {
            return $this->loginValidation;
        }

        if (JWTAuth::attempt($credentials)) {

            $user = JWTAuth::user();

            $token = JWTAuth::fromUser($user);

            $objectToken = JWTAuth::setToken($token);
            $expiration = JWTAuth::decode($objectToken->getToken())->get('exp');

            return responder()->success([
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => $expiration - getdate()[0]
                ]
            ])->respond();
        } else {
            return responder()->error(422, 'Ocorreu um erro de validação.')->data(['errors' => ['fieldname' => 'password', 'message' => 'Usuário ou senha inválidos.']])->respond(422);
        }
    }

    public function loginValidation($request)
    {

        if (!$request->email || !$request->password) {
            if (!$request->email && !$request->password) {
                $this->loginValidation = responder()->error(422, 'Ocorreu um erro de validação.')->data(['errors' => [['fieldname' => 'email', 'message' => 'O campo Email é obrigatório.'], ['fieldname' => 'password', 'message' => 'O campo Senha é obrigatório.']]])->respond(422);
            } else if (!$request->email) {
                $this->loginValidation = responder()->error(422, 'Ocorreu um erro de validação.')->data(['errors' => ['fieldname' => 'email', 'message' => 'O campo Email é obrigatório.']])->respond(422);
            } else {
                $this->loginValidation = responder()->error(422, 'Ocorreu um erro de validação.')->data(['errors' => ['fieldname' => 'password', 'message' => 'O campo Senha é obrigatório.']])->respond(422);
            }
        } else {
            return true;
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        if (JWTAuth::user()) {
            $user = [
                'id'         => JWTAuth::user()->id,
                'name'       => JWTAuth::user()->name,
                'email'      => JWTAuth::user()->email,
                'created_at' => JWTAuth::user()->created_at->format('d/m/Y H:i:s')
            ];
            return responder()->success(['user' => $user])->respond();
        } else {
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (JWTAuth::user()) {
            Auth::logout();
            return responder()->success(['message' => 'Logout efetuado com sucesso!'])->respond();
        } else {
            return responder()->error(401, 'Acesso negado.')->respond(401);
        }

        // auth()->logout();

        // return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::parseToken()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return responder()->success([
            'token' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ])->respond();
    }
}
