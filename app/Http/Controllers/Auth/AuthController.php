<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Authentication Type Word.
     *
     * @var string
     */
    protected $authTokenType  = 'Bearer';

    public function loginid()
    {
        dd('here');
    }

    /**
     * Generating an common response message here
     *
     * @param $user $user [Login User Details]
     * @param $token $token [Login user token]
     * @param $tokenType $tokenType [Returning an token type]
     *
     * @return object
     */
    public function responseToken($user, $token, $tokenType = null)
    {
        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => $tokenType ?? $this->authTokenType
        ]);
    }

    /**
     * Create an Fresh Authentication token for login user.
     *
     * @param $user $user
     *
     * @return string
     */
    public function createAuthToken($user)
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Registering an User with provided valid details.
     *
     * @param RegisterUserRequest $request
     *
     * @return void
     */
    public function register(RegisterUserRequest $request)
    {
        $user = $request->store();
        $token = $this->createAuthToken($user);
        return $this->responseToken($user, $token);
    }

    /**
     * Login an User with login credentials.
     *
     * @param Request $request 
     *
     * @return void
     */
    public function login(LoginUserRequest $request)
    {
        /** Checking an valid user credentials details, else returning an throw when wrong credentials */
        if ($request->checkValidLoginUser()) {
            $user = User::where('email', $request['email'])->firstOrFail();
            $token = $this->createAuthToken($user);
            return $this->responseToken($user, $token);
        }
    }

    /**
     * To generate an refresh token with new 
     *
     * @param Request $request
     *
     * @return void
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        /** Removing an current access token to create new one */
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();
        $user->currentAccessToken()->delete();
        return $this->responseToken($user, $this->createAuthToken($user));
    }

    /**
     * Returning an Login user details by given valid access token.
     *
     * @param Request $request [Accepting an Token Request Details]
     *
     * @return void
     */
    public function me(Request $request)
    {
        if ($user = $request->user()) {
            return response()->json(['user' => $user]);
        } else {
            return response()->json([
                'error' => "Invalid Token"
            ]);
        }
    }

    
    /**
     * Method forgotPassword
     *
     * @param ForgotPasswordRequest $request [explicite description]
     *
     * @return void
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );
        // dd('here', __($status), Password::RESET_LINK_SENT, $status, $request->validated());
    }

    
    /**
     * Method resetPassword
     *
     * @param ResetPasswordRequest $request [explicite description]
     *
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request) {
        dd('Reset', $request->validated());

        // $status = Password::reset(
        //     $request->only('email', 'password', 'password_confirmation', 'token'),
        //     function ($user, $password) {
        //         $user->password = $password;
        //         // $user->forceFill([
        //         //     'password' => Hash::make($password)
        //         // ]);
        //         $user->setRememberToken(Str::random(60));

        //         $user->save();

        //         event(new PasswordReset($user));
        //     }
        // );

        // dd('password status', Password::PASSWORD_RESET);
    }
}
