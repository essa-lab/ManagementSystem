<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\CustomPasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserPasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'frontend_reset_url' => 'sometimes|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        // Generate reset token
        $token = Password::broker('users')->createToken($user);
        info($token);
        try {
            // Send custom notification
            $user->notify(new CustomPasswordResetNotification($token, $request->frontend_reset_url));

            return response()->json(['message' => __('messages.password_reset')], 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json(['error' => __('messages.unable_to_reset_password')], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>'required|exists:password_reset_tokens,email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $status = Password::broker('users')->reset(
            [
                'email'=>$request->email,
                'token' => $request->token,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation
            ],
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
        info($status);
        return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => __('messages.password_reset')], 200)
        : response()->json(['message' => __('messages.unable_to_reset_password')], 400);
    }
}