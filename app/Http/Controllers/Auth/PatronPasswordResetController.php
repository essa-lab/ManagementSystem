<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patron;
use App\Notifications\ActivateAccountNotification;
use App\Notifications\CustomPasswordResetNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PatronPasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:patrons,email',
            'frontend_reset_url' => 'sometimes|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Patron::where('email', $request->email)->first();

        if(!$user->verified){
            return response()->json(['error' =>__('messages.not_verified')], 422);

        }
        // Generate reset token
        $token = Password::broker('patrons')->createToken($user);

        try {
            // Send custom notification
            $user->notify(new CustomPasswordResetNotification($token, $request->frontend_reset_url));

            return response()->json(['message' => __('messages.password_reset_link_sent')], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => __('messages.unable_to_send_reset_link'), 'error' =>  $e->getMessage()], 500);
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
            return response()->json(
                [
                    'message' => __('messages.invalid_reset_password'),
                    'errors' => $validator->errors(),
                ],
                400,
            );
        }

        $status = Password::broker('patrons')->reset(
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

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __('messages.password_reset')], 200)
            : response()->json(['message' => __('messages.unable_to_reset_password')], 400);
    }

    public function activateAccount(string $token){
        try{
            $patron = Patron::where('remember_token',$token)->first();
            if(!$patron){
                return response()->json([
                    'message'=>__('messages.patron_not_found')
                ], 404);
            }
            if($patron->verified){
                return response()->json([
                    'message'=>__('messages.patron_active')
                ], 406);
            }
    
            $patron->verified = 1;
            $patron->verified_at = Carbon::now();
    
            $patron->save();
        }catch(Exception $e){
            return response()->json([
                'message'=>$e->getMessage()
            ], 404);
        }
        

        return response()->json([
            'message'=>__('messages.patron_activated')
        ], 200);
    }
    public function resendActivationEmail(Request $request){
        $patron = auth()->guard('patron')->user();
        
        $patron->notify(new ActivateAccountNotification($patron, $request->get('frontend_activate_url',null)));
        return response()->json([
            'message'=>__('messages.activation_resent')
        ], 200);
    }
}
