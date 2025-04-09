<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Http\Requests\User\UserFormRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{

    public function index(UserRequest $request)
    {
        $request->validated();
        $users = User::query();
        //currentUser
        $user = Auth::user();
        Authorize::hasPermission($user,'USER',$user->library_id,true);
        if(!Authorize::isSuperAdmin($user)){
            $users->where('library_id',$user->library_id);
        }
        if(Authorize::isSuperAdmin($user) && $request->has('library_id')){
            $users->where('library_id',$request->get('library_id'));
        }
        if($request->has('loadRelation')){
            $relationsArray = array_map('trim', explode(',', $request->get('loadRelation')));

            $users->with($relationsArray);
        }
        if($request->has('search')){
            $searchTerm = $request->get('search');
            $users->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%'.$searchTerm.'%')
                      ->orWhere('email', 'like', '%'.$searchTerm.'%');
            });
        }
        if($request->has('status')){
            $users->where('status',$request->get('status'));
        }
        $users->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $users = $users->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($users,UserResource::class));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(UserFormRequest $request)
    {
        $data = $request->validated();
        try{
        $user = Auth::user();
        if(!Authorize::isSuperAdmin($user) && $data['role']=='super_admin'){
            return ApiResponse::sendError(__('messages.unathorized'));
        }
        if(isset($data['library_id'])){
            Authorize::hasPermission($user,'USER',$data['library_id'],true);
        }
        $data['password'] = Hash::make($request->get('password'));
        $user = User::create($data);
        if(isset($data['privilages'])){
            $user->privilages()->attach($data['privilages']); 
        }
        return ApiResponse::sendResponse(__('messages.user_created'),new UserResource($user));
        }catch(HttpException $e){
            Logger::log('Error Creating new user : '.$e->getMessage());
            return ApiResponse::sendError(__('messages.not_authorized'),403);
        }catch(Exception $e){
            Logger::log('Error Creating new user : '.$e->getMessage());
            return ApiResponse::sendError(__('messages.user_create_error'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowUserRequest $request,string $id)
    {
        $request->validated();
        try{
            $user = User::with(['library','privilages'])->find($id);

            Authorize::hasPermission( Auth::user(),'USER',$user->library_id,true);
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'),403);
        }
        if(!$user){
            return ApiResponse::sendError(__('messages.user_not_found'));
        }
        if($request->has('loadRelation')){
            $relationsArray = array_map('trim', explode(',', $request->get('loadRelation')));
            $user->load($relationsArray);
        }
        return ApiResponse::sendResponse(__('messages.get_user'),new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserFormRequest $request, string $id)
    {
        $data = $request->validated();
        try{
            $user = User::findOrFail($id);
            //current User
            $currentUser = Auth::user();
            Authorize::hasPermission($currentUser,'USER',$request->get('library_id',null),true);
            Authorize::canUpdateUser($currentUser,$user);
            if(!Authorize::isSuperAdmin($currentUser) && $request->get('role','')=='super_admin'){
                return ApiResponse::sendError(__('messages.not_authorized'));
            }
            if(isset($data['password'])){
                $data['password'] = Hash::make($request->get('password'));
            }
            $user->update($data);

            if(isset($data['privilages']) ){
                $user->privilages()->sync($data['privilages']); 
            }
            if(isset($data['status'])&& $data['status']=='inactive'){
                $user->suspend();
            }
        return ApiResponse::sendResponse(__('messages.user_update'));
        
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'),403);
        }catch(Exception $e){
            Logger::log('Error updating user : '.$e->getMessage());
            return ApiResponse::sendError(__('messages.user_update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        //current user
        $currentUser = Auth::user();
        if(!$user){
            return ApiResponse::sendError(__('messages.user_not_found'));
        }
        try{
            Authorize::hasPermission($currentUser,'USER',$user->library_id,true);
            Authorize::canUpdateUser($currentUser,$user);
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'),403);

        }
        if($user->id == $currentUser->id){
            return ApiResponse::sendError(__('messages.cannot_delete_yourself'));
        }
        $user->delete();
        return ApiResponse::sendResponse(__('messages.user_delete'));
    }

    public function updateProfile(UpdateUserProfileRequest $request){
        $data = $request->validated();
        try{
            
            $currentUser = Auth::user();
            if(isset($data['password'])){
                $data['password'] = Hash::make($request->get('password'));
            }
            
            $currentUser->update($data);

        return ApiResponse::sendResponse(__('messages.user_update'));
        
        }catch(Exception $e){
            Logger::log('Error updating user : '.$e->getMessage());
            return ApiResponse::sendError(__('messages.user_update_error'));
        }
    }
    public function selfRegisteration(Request $request){
        $validator = Validator::make($request->all(), [
            'enable' => 'required|boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        // Correct variable name
        $isEnabled = $request->input('enable');
        
        // Update the 'self_registration' column in the global_settings table
        DB::table('global_settings')->update(['self_registeration' => $isEnabled]);
        
        return response()->json(['message' => 'Self-registration setting updated successfully']);
    }
    public function getSelfRegisteration(){
    
        return response()->json(['message' => 'Self-registration setting successfully',
        'enable'=>DB::table('global_settings')->value('self_registeration')]);
    }
}
