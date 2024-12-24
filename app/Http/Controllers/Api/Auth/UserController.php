<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSkill;
use App\Rules\ValidPhoneNumber;
use App\Traits\ImageProcessing;
use Ichtrojan\Otp\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    use ImageProcessing;
    public function updateUserInfo(Request $request)
    {
        try {
            $user = $request->user;
            $validator = Validator::make($request->all(), [
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'academic_level' => ['required', 'string', 'max:50'],
                'name' => 'sometimes|string',
                'image' => 'sometimes|image|mimes:jpeg,jpg,png|max:20000',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $arr = $request->only(['email', 'academic_level', 'name']);
            $user = User::findOrFail($user->id);
            if ($request->hasFile('image')) {
                $image = $this->saveImage($request->file('image'), 'user');
                $arr['image'] =  'imagesfp/user/' . $image;
            }
            $user->update($arr);

            return response()->json(['message' => 'Success', 'status_code' => 200, "user" => $user], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => __('custom.server_issue') . $th, 'status_code' => 404,], 404);
        }
    }



    public function changePassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8|different:old_password',
                'new_password_confirmation' => 'required|string|same:new_password',
            ]);
            $user = $request->user;
            if (!Hash::check($validatedData['old_password'], $user->password)) {
                return response()->json(['message' => __('custom.old_password_incorrect'), 'status_code' => 422], 422);
            }

            $user->update(['password' => Hash::make($validatedData['new_password'])]);

            return response()->json(['message' => 'Success', 'status_code' => 200,], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => __('custom.server_issue'), 'status_code' => 404,], 404);
        }
    }


    public function getUserInfo(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $accessToken = PersonalAccessToken::findToken($token);
            if (!$accessToken) {
                return response()->json(['message' => __('custom.unauthorized'), 401], 401);
            }

            $user = $accessToken->tokenable;
            return $user;
        } catch (\Throwable $th) {
            return response()->json(['message' => __('custom.server_issue'), 'status_code' => 404,], 404);
        }
    }
    public function getUserProfile(int $id)
    {
        try {
            $user = User::find($id);
            $userId = $user->id;
            $userData = User::with([
                'evaluations' => function ($query) use ($id) {
                    $query->where('owner_id', $id);
                },
                'skills',
                "profession",
                'services'
            ])->find($user->id);

            return successResponse(["user" => $userData]);
        } catch (\Throwable $th) {
            return errorResponse("Something went wrong", 500);
        }
    }

    public function getOtpForUser(Request $request)
    {
        echo $request->email . "+";
        $email = $request->email;
        $otp = DB::table('otps')->where('identifier', "+" . $email)->orderBy('id', 'desc')->get();
        return response()->json(['otp' => $otp], 200);
    }
}
