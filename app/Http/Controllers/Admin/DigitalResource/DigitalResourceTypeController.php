<?php

namespace App\Http\Controllers\Admin\DigitalResource;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalResource\DigitalResourceType\DigitalResourceStoreRequest;
use App\Http\Requests\DigitalResource\DigitalResourceType\DigitalResourceTypeRequest;
use App\Http\Resources\DigitalResource\DigitalTypeResource;
use App\Http\Resources\PaginatingResource;
use App\Models\DigitalResource\DigitalResourceType;
use Illuminate\Support\Facades\Auth;


class DigitalResourceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DigitalResourceTypeRequest $request)
    {
        //
        $request->validated();
        $digitalResourceType = DigitalResourceType::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $digitalResourceType->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $digitalResourceType->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $digitalResourceType = $digitalResourceType->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($digitalResourceType,DigitalTypeResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DigitalResourceStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $digitalResourceType = DigitalResourceType::create($data);

        Logger::log($digitalResourceType);

        return ApiResponse::sendResponse(__('messages.digitalResourceType_create'),new DigitalTypeResource($digitalResourceType));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new digitalResourceType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.digitalResourceType_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $digitalResourceType = DigitalResourceType::find($id);
        if(!$digitalResourceType){
            return ApiResponse::sendError(__('messages.digitalResourceType_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_digitalResourceType'),new DigitalTypeResource($digitalResourceType));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DigitalResourceStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $digitalResourceType = digitalResourceType::findOrFail($id);
            $digitalResourceType->update($data);
            
        return ApiResponse::sendResponse(__('messages.digitalResourceType_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating digitalResourceType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.digitalResourceType_update_error'));
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
        $digitalResourceType = DigitalResourceType::find($id);
         if(!$digitalResourceType){
            return ApiResponse::sendError(__('messages.digitalResourceType_not_found'));
         }
         $digitalResourceType->delete();
         return ApiResponse::sendResponse(__('messages.digitalResourceType_delete'));
        }
}
