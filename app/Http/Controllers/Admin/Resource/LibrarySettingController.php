<?php

namespace App\Http\Controllers\Admin\Resource;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\LibrarySetting\LibrarySettingRequest;
use App\Http\Requests\Resource\LibrarySetting\LibrarySettingStoreRequest;
use App\Models\Resource\LibrarySetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class LibrarySettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LibrarySettingRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $request->validated();
        $librarySetting = LibrarySetting::query();
        $librarySetting->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));

        $librarySetting = $librarySetting->get();

        foreach($librarySetting as $setting){
            $setting['scheduler_time'] =  Carbon::parse($setting['scheduler_time'])->format('H:i');

        }
              return ApiResponse::sendResponse('library settings',$librarySetting);       
            }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $librarySetting = LibrarySetting::find($id);
        if(!$librarySetting){
            return ApiResponse::sendError(__('messages.librarySetting_not_found'));
        }
        $librarySetting['scheduler_time'] =  Carbon::parse($librarySetting['scheduler_time'])->format('H:i');

        return ApiResponse::sendResponse(__('messages.get_librarySetting'),$librarySetting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LibrarySettingStoreRequest $request,      $id)
    {
        $data = $request->validated();
        try{
            if(!Authorize::isSuperAdmin(Auth::user())){
                return ApiResponse::sendError(__('messages.not_authorized'));
            }
            $librarySetting =LibrarySetting::findOrFail($id);
            $librarySetting->update($data);
        return ApiResponse::sendResponse(__('messages.librarySetting_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating librarySetting : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.librarySetting_update_error'));
        }
    }


}
