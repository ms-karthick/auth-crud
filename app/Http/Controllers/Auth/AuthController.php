<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;

    /**
     * Auth related functionalities.
     *
     * @var AuthRepository
     */
    public $authRepository;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(AuthRepository $ar)
    {
        $this->authRepository = $ar;
    }



    public function register(RegisterRequest $request): JsonResponse
    {
        try{
            $requestData = $request->only('name', 'email', 'password', 'password_confirmation');
            $user = $this->authRepository->register($requestData);
            if ($user) {
                $accessToken  = $user->createToken('authToken')->accessToken;
                if($accessToken){
                    $data =  $this->respondWithToken($accessToken);
                    return $this->responseSuccess($data, 'User Registered and Logged in Successfully', Response::HTTP_OK);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return $this->responseError(null, 'Invalid Email and Password !', Response::HTTP_UNAUTHORIZED);
        }
            $user = $this->guard()->user();
            $token = $user->createToken('authToken')->accessToken;
               // $refreshToken =  $user->createToken('authRefreshToken',['server:refresh-token'])->accessToken;

            if ($token) {
                $data =  $this->respondWithToken($token);
                return $this->responseSuccess($data, 'Logged In Successfully !');
            }
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

 
    public function logout(): JsonResponse
    {
        try {
            $user =  $this->guard()->user()->token();
            $user->revoke();
            return $this->responseSuccess(null, 'Logged out successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    

    public function logged_user(): JsonResponse
    {
        try {
            $data = $this->guard()->user();
            return $this->responseSuccess($data, 'Profile Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

   
    public function change_password(Request $request){
        try{
            $request->validate([
                'password' => 'required|confirmed',
            ]);
            $loggeduser = Auth::user();
            $loggeduser->password = Hash::make($request->password);
            $loggeduser->save();
            return $this->responseSuccess(null, 'Password Changed Successfully !');

        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }    
    }

    protected function respondWithToken($token): array
    {        
        $data = [[
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::user()->getAccessTokenExpireTime()->format('Y-m-d H:i:s'),
            // 'expires_in' => $this->guard()->factory()->getTTL() * 60 * 24 * 30, // 43200 Minutes = 30 Days
            'user' => $this->guard()->user()
        ]];
        return $data[0];
    }

      /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard(): \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
    {
        return Auth::guard();
    }

    public function common(){
        
        $now = now('Asia/Kolkata')->format('Y-m-d H:i:s');
        return ($now);
    }
}