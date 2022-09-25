<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use App\Adapters\RequestValidAdapter;
use App\Adapters\ResponseAdapter;
use App\Repositories\UserRepository;

class AuthController extends Controller
{

    private $requestValidAdapter;
    private $userRepository;
    private $responseAdapter;

    public function __construct(
        RequestValidAdapter $requestValidAdapter,
        UserRepository $userRepository,
        ResponseAdapter $responseAdapter
    ) {
        $this->requestValidAdapter = $requestValidAdapter;
        $this->userRepository = $userRepository;
        $this->responseAdapter = $responseAdapter;
    }

    public function register(Request $request): object
    {

        $validator = $this->isValidUserDataRegister($request->all());
        if (!$validator->status) {
            return $this->responseAdapter->sendResponse(
                "error_validation",
                "Error en la validación de los datos del usuario.",
                $validator->messages,
                200
            );
        }

        $user = $this->userRepository->storeUser((object)$request->all());

        $credentials = $request->only('email', 'password');
        $response = [
            'token' => JWTAuth::attempt($credentials),
            'user' => $user
        ];

        return $this->responseAdapter->sendResponse(
            "success",
            "Usuario registrado con exito.",
            $response,
            200
        );
    }

    public function login(Request $request): object
    {
        $credentials = $request->only('email', 'password');

        $validator = $this->isValidUserDataLogin($credentials);
        if (!$validator->status) {
            return $this->responseAdapter->sendResponse(
                "error_validation",
                "Error en la validación de los datos del usuario.",
                $validator->messages,
                200
            );
        }

        //Intentamos hacer login
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->responseAdapter->sendResponse(
                    "error_login",
                    "Los datos ingresados son incorrectos.",
                    [],
                    200
                );
            }
        } catch (JWTException $e) {
            return $this->responseAdapter->sendResponse(
                "error_validation",
                "Error al tratar de iniciar sesión.",
                $e->getMessage(),
                500
            );
        }

        $response = [
            'token' => $token,
            'user' => Auth::user()
        ];

        return $this->responseAdapter->sendResponse(
            "success",
            "Sesión iniciada.",
            $response,
            200
        );
    }

    public function logout(Request $request)
    {
        //Validamos que se nos envie el token
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            //Si el token es valido eliminamos el token desconectando al usuario.
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User disconnected'
            ]);
        } catch (JWTException $exception) {
            //Error chungo
            return response()->json([
                'success' => false,
                'message' => 'Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function isValidUserDataRegister(array $request): object
    {
        return $this->requestValidAdapter->isRequestValid($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
        ]);
    }

    public function isValidUserDataLogin(array $request): object
    {
        return $this->requestValidAdapter->isRequestValid($request, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);
    }
}
