<?php

namespace App\Http\Controllers\Admin\Resource;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\Source\SourceRequest;
use App\Http\Requests\Resource\Source\SourceStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\SourceResource;
use App\Models\Resource\Source;
use Illuminate\Support\Facades\Auth;


class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SourceRequest $request)
    {
        //
        $request->validated();
        $source = Source::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $source->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }


        $source->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $source = $source->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($source,SourceResource::class));    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SourceStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $source =Source::create($data);

        Logger::log($source);

        return ApiResponse::sendResponse(__('messages.source_create'),new SourceResource($source));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new source : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.source_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $source = Source::find($id);
        if(!$source){
            return ApiResponse::sendError(__('messages.source_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_source'),new SourceResource($source));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SourceStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $source =Source::findOrFail($id);
            $source->update($data);
        return ApiResponse::sendResponse(__('messages.source_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating source : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.source_update_error'));
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
        $source = Source::find($id);
         if(!$source){
            return ApiResponse::sendError(__('messages.source_not_found'));
         }
         $source->delete();
         return ApiResponse::sendResponse(__('messages.source_delete'));
        }
}
