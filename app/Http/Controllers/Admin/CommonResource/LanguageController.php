<?php

namespace App\Http\Controllers\Admin\CommonResource;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\Language\LanguageRequest;
use App\Http\Requests\Resource\Language\LanguageStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\LanguageResource;
use App\Models\Resource\Language;
use Illuminate\Support\Facades\Auth;


class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LanguageRequest $request)
    {
        //
        $request->validated();
        $language = Language::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $language->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }


        $language->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $language = $language->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($language,LanguageResource::class));       }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $language =Language::create($data);

        Logger::log($language);

        return ApiResponse::sendResponse(__('messages.language_create'),new LanguageResource($language));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new language : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.language_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $language = Language::find($id);
        if(!$language){
            return ApiResponse::sendError(__('messages.language_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_language'),new languageResource($language));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $language =Language::findOrFail($id);
            $language->update($data);
        return ApiResponse::sendResponse(__('messages.language_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating Language : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.language_update_error'));
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
        $language = Language::find($id);
         if(!$language){
            return ApiResponse::sendError(__('messages.language_not_found'));
         }
         $language->delete();
         return ApiResponse::sendResponse(__('messages.language_delete'));
        }
}
