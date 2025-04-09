<?php

namespace App\Http\Controllers\Auth;

use App\Helper\Authorize;
use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Privilage;
use App\Models\User;
use App\Traits\FilePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTUserAuthController extends Controller
{

    // Constructor
    public function __construct()
    {
        Config::set('jwt.user', User::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ]]);
    }

    // User login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $key = 'login_attempts_' . $request->get('email');
            RateLimiter::hit($key, 120);

            if (RateLimiter::tooManyAttempts($key, 5)) { 
                return response()->json(['message' => __('messages.throttle')], 429);
            }
            
            if (!$accessToken = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => __('messages.invalid_credentials')], 401);
            }

            $user = auth()->user();
            if($user->status=='inactive'){
                return response()->json(['error' => __('messages.user_susbend')], 401);
            }

            // Generate separate access and refresh tokens with guard claim
            $refreshToken = JWTAuth::claims(['type' => 'refresh', 'guard' => 'user'])->fromUser($user);
            $accessToken = JWTAuth::claims(['type' => 'access', 'guard' => 'user'])->fromUser($user);

            return response()->json([
                'user' => new \App\Http\Resources\UserResource($user),
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
            $refreshToken = $request->cookie('refresh_token');
            if (!$refreshToken) {
                return response()->json(['error' => __('messages.could_not_create_token')], 401);
            }

            JWTAuth::setToken($refreshToken);

            // Verify the token is valid and not expired
            if (!JWTAuth::check()) {
                return response()->json(['error' => __('messages.token_invalid')], 401);
            }

            $payload = JWTAuth::getPayload();
            if ($payload['type'] !== 'refresh') {
                return response()->json(['error' => __('messages.token_invalid')], 401);
            }

            // Get user from token subject
            $user = User::find($payload->get('sub'));
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Generate new tokens with guard claim
            $newAccessToken = JWTAuth::claims(['type' => 'access', 'guard' => 'user'])->fromUser($user);
            $newRefreshToken = JWTAuth::claims(['type' => 'refresh', 'guard' => 'user'])->fromUser($user);

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

    //update email
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = auth()->user();
        $user->email = $request->email;
        $user->save();

        return response()->json(['message' => __('messages.email_updated')]);
    }

    public function updatePasswordUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'=>'required|string',
            'password'=>'required|string|min:8|confirmed',
            'password_confirmation'=>'required|string'
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

        $userId = auth()->guard('user')->user()->id;
        
        $user = User::find($userId);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => __('messages.old_password_error')], 406);
            
        }

        // Update password
        $user->password=Hash::make($request->password);
        $user->save();
        
        return response()->json(['message' => __('messages.password_updated')], 200);

    }

    //update password
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => __('messages.password_updated')]);
    }

    //get auth user
    public function getUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => __('messages.user_not_found')], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => __('messages.token_invalid')], 401);
        }
        //to-do 
        //add library and privilage
        //for super admin add all libraries 

        // Load library and privileges relations
        $user->load('library', 'privilages');  

        // If the user is a super admin, load all libraries
        if (Authorize::isSuperAdmin($user)) {  
            $user->setRelation('library', Library::all());  
        }
        if (Authorize::isSuperAdmin($user)) {  
            $user->setRelation('privilages', Privilage::all());  
        }
        $user->profile_picture =  $user->profile_picture  ? Storage::url($user->profile_picture ) : null;


        return response()->json(compact('user'));
    }
}
