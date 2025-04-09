<?php

namespace App\Http\Controllers\Admin\Article;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ArticleType\ArticleTypeRequest;
use App\Http\Requests\Article\ArticleType\ArticleTypeStoreRequest;
use App\Http\Resources\Article\ArticleTypeResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Article\ArticleType;
use Illuminate\Support\Facades\Auth;


class ArticleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ArticleTypeRequest $request)
    {
        //

       

        $request->validated();
        $articleType = ArticleType::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $articleType->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $articleType->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $articleType = $articleType->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($articleType,ArticleTypeResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleTypeStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }

        $data = $request->validated();
        try{

        $articleType = ArticleType::create($data);

        Logger::log($articleType);

        return ApiResponse::sendResponse(__('messages.articleType_create'),new ArticleTypeResource($articleType));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new articleType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.articleType_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $articleType = ArticleType::find($id);
        if(!$articleType){
            return ApiResponse::sendError(__('messages.articleType_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_articleType'),new ArticleTypeResource($articleType));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArticleTypeStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $articleType = ArticleType::findOrFail($id);
            $articleType->update($data);
            
        return ApiResponse::sendResponse(__('messages.articleType_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating articleType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.articleType_update_error'));
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
        $articleType = ArticleType::find($id);
         if(!$articleType){
            return ApiResponse::sendError(__('messages.articleType_not_found'));
         }
         $articleType->delete();
         return ApiResponse::sendResponse(__('messages.articleType_delete'));
        }
}
