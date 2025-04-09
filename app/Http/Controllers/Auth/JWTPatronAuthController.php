<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patron\PatronUpdateAccountRequest;
use App\Models\Patron;
use App\Notifications\ActivateAccountNotification;
use App\Notifications\CustomEmailResetNotification;
use App\Notifications\PasswordResetNotification;
use Carbon\Carbon;
use Cookie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Password;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTPatronAuthController extends Controller
{

    // Constructor
    public function __construct()
    {
        Config::set('jwt.patron', Patron::class);
        Config::set('auth.providers', ['patrons' => [
            'driver' => 'eloquent',
            'model' => Patron::class,
        ]]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // Specify the patron guard explicitly
            if (!$accessToken = auth()->guard('patron')->attempt($credentials)) {
                return response()->json(['error' => __('messages.invalid_credentials')], 401);
            }


            // Get patron user using patron guard
            $patron = auth()->guard('patron')->user();

            if ($patron->status == 'inactive') {
                return response()->json(['error' => __('messages.user_susbend')], 401);
            }

            Auth::shouldUse('patron'); 

            $refreshToken = JWTAuth::claims([
                'type' => 'refresh',
                'guard' => 'patron'
            ])->fromUser($patron);
                info('ss');
            $accessToken = JWTAuth::claims([
                'type' => 'access',
                'guard' => 'patron'
            ])->fromUser($patron);

                $patron->last_login_time=Carbon::now();
                $patron->save();
            return response()->json([
                'patron' => new \App\Http\Resources\PatronResource($patron),
                'access_token' => $accessToken,
                'access_expires' => now()->addMinutes(config('jwt.ttl')),
            ])->withCookie(
                cookie('refresh_token', $refreshToken, config('jwt.refresh_ttl') * 60, null, null, true, true)
            );
        } catch (JWTException $e) {
            return response()->json(['error' => __('messages.could_not_create_token')], 500);
        }
    }

    public function refreshTokens(Request $request)
    {
        try {
            // Use patron-specific cookie name
            $refreshToken = $request->cookie('refresh_token');
            if (!$refreshToken) {
                return response()->json(['error' => __('messages.token_invalid')], 401);
            }

            JWTAuth::setToken($refreshToken);

            if (!JWTAuth::check()) {
                return response()->json(['error' => __('messages.token_invalid')], 401);
            }

            $payload = JWTAuth::getPayload();

            // Verify both token type and guard
            if ($payload['type'] !== 'refresh' || $payload['guard'] !== 'patron') {
                return response()->json(['error' => __('messages.token_invalid')], 401);
            }

            // Get patron using Patron model
            $patron = Patron::find($payload->get('sub'));
            if (!$patron) {
                return response()->json(['error' => __('messages.patron_not_found')], 404);
            }

            // Generate new tokens with patron guard claims
            $newAccessToken = JWTAuth::claims([
                'type' => 'access',
                'guard' => 'patron'
            ])->fromUser($patron);

            $newRefreshToken = JWTAuth::claims([
                'type' => 'refresh',
                'guard' => 'patron'
            ])->fromUser($patron);

            return response()->json([
                'access_token' => $newAccessToken,
                'access_expires' => now()->addMinutes(config('jwt.ttl')),
            ])->withCookie(
                cookie('refresh_token', $newRefreshToken, config('jwt.refresh_ttl') * 60, null, null, true, true)
            );
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token refresh failed: ' . $e->getMessage()], 401);
        }
    }

    // Get authenticated patron
    public function getPatron()
    {
        try {
            if (! $patron = Auth::guard('patron')->user()) {
                return response()->json(['error' => __('messages.patron_not_found')], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => __('messages.token_invalid')], 401);
        }

        $patron->profile_picture =  $patron->profile_picture  ? Storage::url($patron->profile_picture ) : null;

        return response()->json(compact('patron'));
    }



    // User logout
    public function logout()
    {
        try {
            // Invalidate the current access token
            JWTAuth::invalidate(JWTAuth::getToken());

            // Clear the refresh token cookie by setting an expired cookie
            $cookie = cookie('refresh_token', null, -1, null, null, true, true);

            return response()->json(['message' => __('messages.successfully_logged_out')])
                ->withCookie($cookie);
        } catch (JWTException $e) {
            return response()->json(['error' => __('messages.logout_failed')], 500);
        }
    }

    public function updateAccount(PatronUpdateAccountRequest $request)
    {
        $request->validated();
        $patron = auth()->guard('patron')->user();

        $patron->university = $request->get('university',$patron->university);
        $patron->college = $request->get('college',$patron->college);
        $patron->phone = $request->get('phone',$patron->phone);
        $patron->address = $request->get('address',$patron->address);
        $patron->internal_identifier = $request->get('internal_identifier',$patron->internal_identifier);
        $patron->status = $request->get('status',$patron->status);
        $patron->profile_picture = $request->get('profile_picture',$patron->profile_picture);
        $patron->locale = $request->get('locale',$patron->locale);
        $patron->name = $request->get('name',$patron->name);
        $patron->occupation = $request->get('occupation',$patron->occupation);

        $patron->save();

        if($request->get('status','') =='inactive'){
            JWTAuth::invalidate(JWTAuth::getToken());
        }
        return response()->json(['message' => __('messages.patron_update')]);
    }

    public function requestUpdateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'frontend_reset_url' => 'sometimes|url',
            'new_email'=>'required|email|unique:patrons,email',
            'password'=>'required|string|min:8'
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $userId = auth()->guard('patron')->user()->id;
        $user = Patron::find($userId);
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' =>  __('messages.old_password_error')], 406);
        }   

        try {
            // Send custom notification
            Notification::route('mail', $request->get('new_email'))->notify(new CustomEmailResetNotification($request->get('new_email'), $user->remember_token, $request->frontend_reset_url));
            info($user->remember_token);
            $cookie = Cookie::make('new_email_' . $user->id, $request->get('new_email'), 60); 

            return response()->json(['message' => __('messages.Email_reset_link_sent')], 200)->withCookie($cookie);
        } catch (\Exception $e) {
            Cookie::forget('new_email_' . $user->id);
            return response()->json(['message' => __('messages.unable_to_send_reset_link'), 'error' =>  $e->getMessage()], 500);
        }

    }

    
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
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

        $user = Patron::where('remember_token',$request->token)->first();
        if(!$user){
            return response()->json(['message' => __('messages.token_invalid')], 404);
        }
        try{
            $user->email= $request->cookie('new_email_' . $user->id);
            $user->remember_token = Str::random(24);
            $user->save();
            $cookie = Cookie::forget('new_email_' . $user->id);
    
        }catch(Exception $e){
            return response()->json(['message' => __('messages.token_invalid')], 404);

        }
        
        return response()->json(['message' => __('messages.email_updated')], 200)->withCookie($cookie);

    }

    // public function requestUpdatePassword(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'frontend_reset_url' => 'sometimes|url'
    //     ]);


    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }

    //     $user = auth()->guard('patron')->user();

    //      // Generate reset token

    //     try {
    //         // Send custom notification
    //         $user->notify(new PasswordResetNotification($user->remember_token, $request->frontend_reset_url));

    //         return response()->json(['message' => __('messages.password_reset_link_sent')], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => __('messages.unable_to_send_reset_link'), 'error' =>  $e->getMessage()], 500);
    //     }

    // }

    
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'=>'required|string',
'password' => [
        'required',
        'string',
        'confirmed', // Ensures password_confirmation matches password
        RulesPassword::min(8) // Minimum 8 characters
            ->letters() // Must include at least one letter
            ->numbers() // Must include at least one number
            ->symbols() // Must include at least one special character
    ],            'password_confirmation'=>'required|string'
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

        $userId = auth()->guard('patron')->user()->id;
        
        $user = Patron::find($userId);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => __('messages.old_password_error')], 406);
            
        }

        // Update password
        $user->password=Hash::make($request->password);
        $user->save();
        
        return response()->json(['message' => __('messages.password_updated')], 200);

    }

    // User registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patrons,email',
'password' => [
        'required',
        'string',
        'confirmed', // Ensures password_confirmation matches password
        Password::min(8) // Minimum 8 characters
            ->letters() // Must include at least one letter
            ->mixedCase() // Must include both uppercase and lowercase letters
            ->numbers() // Must include at least one number
            ->symbols() // Must include at least one special character
    ],            'frontend_activate_url'=>'sometimes|url',
            'internal_identifier'=>'nullable|string',
            'locale'=>'string|nullable|in:ar,en,ku'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $patron = Patron::create([
            'internal_identifier'=>$request->get('internal_identifier',null),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'remember_token'=>Str::random(24),
            'status'=>'active',
            'verified'=>0,
            'locale'=>$request->get('locale','en')
        ]);

        Auth::shouldUse('patron'); //force to use this guard

        $refreshToken = JWTAuth::claims([
            'type' => 'refresh',
            'guard' => 'patron'
        ])->fromUser($patron);

        $accessToken = JWTAuth::claims([
            'type' => 'access',
            'guard' => 'patron'
        ])->fromUser($patron);

        
        $patron->notify(new ActivateAccountNotification($patron, $request->frontend_activate_url));
        return response()->json([
            'message'=>__('messages.activation'),
            'patron' => new \App\Http\Resources\PatronResource($patron),
            'access_token' => $accessToken,
            'access_expires' => now()->addMinutes(config('jwt.ttl')),
        ])->withCookie(
            cookie('refresh_token', $refreshToken, config('jwt.refresh_ttl') * 60, null, null, true, true)
        );



    }

    
}
