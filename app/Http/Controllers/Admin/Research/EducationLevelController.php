<?php

namespace App\Http\Controllers\Admin\Research;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Research\EducationLevel\EducationLevelRequest;
use App\Http\Requests\Research\EducationLevel\EducationLevelStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Research\EducationLevelResource;
use App\Models\Research\EducationLevel;
use Illuminate\Support\Facades\Auth;


class EducationLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(EducationLevelRequest $request)
    {
        //
        $request->validated();
        $educationLevel = EducationLevel::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $educationLevel->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $educationLevel->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $educationLevel = $educationLevel->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($educationLevel,EducationLevelResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EducationLevelStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $educationLevel = EducationLevel::create($data);

        Logger::log($educationLevel);

        return ApiResponse::sendResponse(__('messages.educationLevel_create'),new EducationLevelResource($educationLevel));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new educationLevel : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.educationLevel_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $educationLevel = EducationLevel::find($id);
        if(!$educationLevel){
            return ApiResponse::sendError(__('messages.educationLevel_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_educationLevel'),new EducationLevelResource($educationLevel));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EducationLevelStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $educationLevel = EducationLevel::findOrFail($id);
        $educationLevel->update($data);

        return ApiResponse::sendResponse(__('messages.educationLevel_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating educationLevel : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.educationLevel_update_error'));
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
        $educationLevel = EducationLevel::find($id);
         if(!$educationLevel){
            return ApiResponse::sendError(__('messages.educationLevel_not_found'));
         }
         $educationLevel->delete();
         return ApiResponse::sendResponse(__('messages.educationLevel_delete'));
        }
}
