<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboa\ArticleType\ArticleTypeRequest;
use App\Http\Requests\Article\ArticleType\ArticleTypeStoreRequest;
use App\Http\Requests\Dashboard\NavigationRequest;
use App\Http\Resources\Article\ArticleTypeResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Article\ArticleType;
use App\Models\Dashboard\Navigation;
use Illuminate\Support\Facades\Auth;


class NavigationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $navigations = Navigation::get();

        return ApiResponse::sendPaginatedResponse($navigations);    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NavigationRequest $request)
    {
        // if(!Authorize::isSuperAdmin(Auth::user())){
        //     return ApiResponse::sendError(__('messages.not_authorized'));
        // }

        $data = $request->validated();
        try{
        $navigation = Navigation::create($data);
        return ApiResponse::sendResponse(__('messages.naviagtion_create'),$navigation);
        
        }catch(\Exception $e){
            Logger::log('Error Creating new naviagtion : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.naviagtion_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $navigation = Navigation::find($id);
        if(!$navigation){
            return ApiResponse::sendError(__('messages.navigation_not_found'));
        }
        return ApiResponse::sendResponse(__('messages.get_navigation'),$navigation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NavigationRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $navigation = Navigation::findOrFail($id);
            $navigation->update($data);
            
        return ApiResponse::sendResponse(__('messages.navigation_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating navigation : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.navigation_update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $navigation = Navigation::find($id);
         if(!$navigation){
            return ApiResponse::sendError(__('messages.navigation_not_found'));
         }
         $navigation->delete();
         return ApiResponse::sendResponse(__('messages.navigation_delete'));
        }
}
