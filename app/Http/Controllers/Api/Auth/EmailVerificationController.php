<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Rules\ValidPhoneNumber;
use App\Traits\Api\AuthTrait;
use App\Traits\WhatsAppTrait;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{

    use AuthTrait, WhatsAppTrait;
    public $otp;

    public function __construct()
    {
        // $this->middleware('auth'); // Ensure user is authenticated
        $this->otp = new Otp;
    }

    public function verificationNotification(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'max:30', 'exists:users,email', 'indisposable'], //
        ]);

        $inpout = $request->email;
        $otp = generateOtp($inpout);
        // send email
        return response()->json(['message' => 'Success', 'status_code' => 200,], 200);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'max:30', 'exists:users,email', 'indisposable'], //
            "otp" => 'required|string|max:4|min:4',
        ]);
        $email = $request->email;
        $otp2 = $this->otp->validate($email, $request->otp);
        if (!$otp2->status) {
            return response()->json(['message' => __('custom.email_verification_error'), 'status_code' => 404], 404);
        }
        $user = $this->getUserByemail($email);
        $user->email_verified_at = now();
        $user->save();
        $token = $user->createToken('Laravel Sanctum')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user, 'message' => 'Success', 'status_code' => 200], 200);
    }
}
