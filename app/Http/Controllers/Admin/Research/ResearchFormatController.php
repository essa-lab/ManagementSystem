<?php

namespace App\Http\Controllers\Admin\Research;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Research\ResearchFormat\ResearchFormatRequest;
use App\Http\Requests\Research\ResearchFormat\ResearchFormatStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Research\ResearchFormatResource;
use App\Models\Research\ResearchFormat;
use Illuminate\Support\Facades\Auth;

class ResearchFormatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResearchFormatRequest $request)
    {
        //
        $request->validated();
        $researchFormat = ResearchFormat::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $researchFormat->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $researchFormat->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $researchFormat = $researchFormat->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($researchFormat,ResearchFormatResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResearchFormatStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $researchFormat = ResearchFormat::create($data);

        Logger::log($researchFormat);

        return ApiResponse::sendResponse(__('messages.researchFormat_create'),new ResearchFormatResource($researchFormat));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new researchFormat : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.researchFormat_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $researchFormat = ResearchFormat::find($id);
        if(!$researchFormat){
            return ApiResponse::sendError(__('messages.researchFormat_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_researchFormat'),new ResearchFormatResource($researchFormat));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResearchFormatStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $researchFormat = ResearchFormat::findOrFail($id);
            $researchFormat->update($data);
            
        return ApiResponse::sendResponse(__('messages.researchFormat_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating researchFormat : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.researchFormat_update_error'));
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
        $researchFormat = ResearchFormat::find($id);
         if(!$researchFormat){
            return ApiResponse::sendError(__('messages.researchFormat_not_found'));
         }
         $researchFormat->delete();
         return ApiResponse::sendResponse(__('messages.researchFormat_delete'));
        }
}
