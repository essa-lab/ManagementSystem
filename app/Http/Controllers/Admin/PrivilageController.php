<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Privilage\PrivilageRequest;
use App\Http\Requests\Privilage\PrivilageStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\PrivilageResource;
use App\Models\Privilage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PrivilageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PrivilageRequest $request)
    {
        $request->validated();
        $privilage = Privilage::query();
        $user = Auth::user();
        try{
            Authorize::hasPermission($user,'PRIVILEGE',null,true);
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.anauthorized'),403);
        }
        
        if(!Authorize::isSuperAdmin($user)){
            $privilage->whereHas('users', function ($query) use ($user){
                $query->where('users.id', $user->id);
            });
        }

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $privilage->where('privilage_name','like','%'.$searchTerm.'%');
        }

        $privilage->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $privilage = $privilage->get();
        return ApiResponse::sendResponse(__('messages.get_previlages'),PrivilageResource::collection($privilage));    
    }

}
