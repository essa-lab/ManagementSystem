<?php

namespace App\Http\Controllers\Admin\Resource;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\StoreResourceSettingRequest;

use App\Http\Resources\Resource\ResourceSettingResource;
use App\Models\Resource\Resource;
use App\Models\Resource\ResourceSetting;
use App\Models\Resource\Subject;
use Illuminate\Support\Facades\Auth;


class ResourceSettingController extends Controller
{

    public function store(StoreResourceSettingRequest $request)
    {
        $data = $request->validated();
        try{
            $setting = ResourceSetting::where('resource_id',$request->get('resource_id'))->first();
            if($setting){
                return ApiResponse::sendError(__('messages.resource_has_setting'));
            }

            $resourceSetting =ResourceSetting::create($data);
            return ApiResponse::sendResponse(__('messages.resource_setting_created'),new ResourceSettingResource($resourceSetting));
        }catch(\Exception $e){
            Logger::log('Error Creating new subject : '.$e->getMessage());
            return ApiResponse::sendError(__('messages.resource_setting_update_error'));
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(StoreResourceSettingRequest $request, int $id)
    {
        // if(!Authorize::isSuperAdmin(Auth::user())){
        //     return ApiResponse::sendError(__('messages.not_authorized'));
        // }
        $data = $request->validated();
        try{

            $resourceSetting =ResourceSetting::findOrFail($id);
            $resourceSetting->update($data);
        return ApiResponse::sendResponse(__('messages.resource_setting_updated'));
        
        }catch(\Exception $e){
            Logger::log('Error updating subject : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.resource_setting_update_error'));
        }
    }

    public function getResourceSetting(string $resourceId){
        $resourceSetting = ResourceSetting::where('resource_id',$resourceId)->first();
        if(!$resourceSetting){
            return ApiResponse::sendError(__('messages.resource_setting_not_found'));
        }
        return ApiResponse::sendResponse(__('messages.resource_setting'),new ResourceSettingResource($resourceSetting));

    }
}
