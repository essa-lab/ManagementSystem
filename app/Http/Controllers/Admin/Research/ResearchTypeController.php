<?php

namespace App\Http\Controllers\Admin\Research;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Research\ResearchType\ResearchTypeRequest;
use App\Http\Requests\Research\ResearchType\ResearchTypeStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Research\ResearchTypeResource;
use App\Models\Research\ResearchType;
use Illuminate\Support\Facades\Auth;


class ResearchTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResearchTypeRequest $request)
    {
        //
        $request->validated();
        $researchType = ResearchType::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $researchType->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $researchType->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $researchType = $researchType->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($researchType,ResearchTypeResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResearchTypeStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $researchType = ResearchType::create($data);

        Logger::log($researchType);

        return ApiResponse::sendResponse(__('messages.researchType_create'),new ResearchTypeResource($researchType));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new researchType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.researchType_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $researchType = ResearchType::find($id);
        if(!$researchType){
            return ApiResponse::sendError(__('messages.researchType_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_researchType'),new ResearchTypeResource($researchType));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResearchTypeStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $researchType = ResearchType::findOrFail($id);
            $researchType->update($data);
            
        return ApiResponse::sendResponse(__('messages.researchType_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating researchType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.researchType_update_error'));
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
        $researchType = ResearchType::find($id);
         if(!$researchType){
            return ApiResponse::sendError(__('messages.researchType_not_found'));
         }
         $researchType->delete();
         return ApiResponse::sendResponse(__('messages.researchType_delete'));
        }
}
