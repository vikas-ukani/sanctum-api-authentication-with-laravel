<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    protected $authTokenType  = 'Bearer';

    public function responseToken($user, $token, $tokenType = null )
    {
        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => $tokenType ?? $this->authTokenType
        ]);
    }

    public function createAuthToken($user)
    {
        return $user->createToken('auth_token')->plainTextToken;
    }


    /**
     * Method register
     *
     * @param RegisterUserRequest $request [explicite description]
     *
     * @return void
     */
    public function register(RegisterUserRequest $request)
    {
        $user = $request->store();
        $token = $this->createAuthToken($user);
        return $this->responseToken($user, $token );
    }

    /**
     * Method login
     *
     * @param Request $request [explicite description]
     *
     * @return void
     */
    public function login(LoginUserRequest $request)
    {
        if ($request->checkValidLoginUser()) {
            $user = User::where('email', $request['email'])->firstOrFail();
            $token = $this->createAuthToken($user);
            return $this->responseToken($user, $token );
        }
    }
    
    /**
     * Method refresh
     *
     * @param Request $request [explicite description]
     *
     * @return void
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();
        $user->currentAccessToken()->delete();
        return $this->responseToken($user, $this->createAuthToken($user));
    }

    public function me(Request $request)
    {
        if ($user = $request->user()) {
            return response()->json(['user' => $user]);
            // dd('User', $request->user()->tokens(), $request);
        } else {
            return response()->json([
                'error' => "Invalid Token"
            ]);
        }
    }
}
