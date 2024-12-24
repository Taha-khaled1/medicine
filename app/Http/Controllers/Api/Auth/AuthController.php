<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SocialRegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Api\AuthTrait as ApiAuthTrait;
use App\Traits\AuthTrait;
use Ichtrojan\Otp\Otp;
use App\Traits\WhatsAppTrait;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
// __('custom.')
class AuthController extends Controller
{
    use Notifiable, WhatsAppTrait, ApiAuthTrait;
    private $auth;
    public $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            throw new \Illuminate\Auth\AuthenticationException(__('custom.authentication_failed'));
        }
        $user = auth()->user();
        if ($user->status == '0') {
            throw new \Illuminate\Auth\AuthenticationException(__('custom.user_blocked'));
        }
        if (!$user->email_verified_at) {
            return response()->json(['message' => 'ارجوك فعل الحساب الخاص بك', 'status_code' => 404], 404);
        }
        $token = $this->createTokenForUser($user);
        $this->updateFcm($user, $request->fcm);
        return response()->json(['token' => $token, "user" => $user, 'message' => 'Success', 'status_code' => 200], 200);
    }



    public function logout(Request $request)
    {
        $request->user->tokens()->delete();
        return response()->json(['message' => 'Success', 'status_code' => 200,], 200);
    }


    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->createUser($request->validated());
            $token = $this->createTokenForUser($user);
            $inpout = $user->email;
            $otp = generateOtp($inpout);
            // send email

            return response()->json([
                'token' => $token,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return errorResponse($th->getMessage());
        }
    }



    public function socialRegister(SocialRegisterRequest $request)
    {
        try {
            $userData = $request->all();
            $request['password'] = 'social-register';
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'image' => $userData['image'],
                    'phone' => '00000000000',
                    'fcm' => $userData['fcm'] ?? "test",
                    'email_verified_at' => now(),
                    "invitation_code" => generateUniqueInvitationCode(),
                    'password' => Hash::make($request['password']),
                    "login_type" => $userData['login_type'],
                ]);
            } else {
                $credentials = $request->only(['email', 'password']);
                if (!Auth::attempt($credentials)) {
                    return response()->json(
                        [
                            'message' => __('custom.auth_arror'),
                            'status_code' => 409
                        ],
                        409
                    );
                }

                $user = Auth::user();
            }
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user, 'message' => 'Success', 'status_code' => 200], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status_code' => 500], 500);
        }
    }
}
