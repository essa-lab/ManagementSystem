<?php

namespace App\Http\Controllers\Admin\Book;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Books\PoetryCollection\PoetryCollectionRequest;
use App\Http\Requests\Books\PoetryCollection\PoetryCollectionStoreRequest;
use App\Http\Resources\Book\PoetryCollectionResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Book\PoetryCollection;
use Illuminate\Support\Facades\Auth;


class PoetryCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PoetryCollectionRequest $request)
    {
        //
        $request->validated();
        $poetryCollection = PoetryCollection::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $poetryCollection->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $poetryCollection->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $poetryCollection = $poetryCollection->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($poetryCollection,PoetryCollectionResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PoetryCollectionStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $poetryCollection = PoetryCollection::create($data);

        Logger::log($poetryCollection);

        return ApiResponse::sendResponse(__('messages.poetryCollection_create'),new PoetryCollectionResource($poetryCollection));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new poetryCollection : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.poetryCollection_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $poetryCollection = PoetryCollection::find($id);
        if(!$poetryCollection){
            return ApiResponse::sendError(__('messages.poetryCollection_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_poetryCollection'),new PoetryCollectionResource($poetryCollection));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PoetryCollectionStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $poetryCollection = PoetryCollection::findOrFail($id);
            $poetryCollection->update($data);
            
        return ApiResponse::sendResponse(__('messages.poetryCollection_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating poetryCollection : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.poetryCollection_update_error'));
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
        $poetryCollection = PoetryCollection::find($id);
         if(!$poetryCollection){
            return ApiResponse::sendError(__('messages.poetryCollection_not_found'));
         }
         $poetryCollection->delete();
         return ApiResponse::sendResponse(__('messages.poetryCollection_delete'));
        }
}
