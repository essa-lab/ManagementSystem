<?php

namespace App\Http\Controllers\Admin\Article;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\Specification\SpecificationRequest;
use App\Http\Requests\Article\Specification\SpecificationStoreRequest;
use App\Http\Resources\Article\SpecificationResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Article\Specification;
use Illuminate\Support\Facades\Auth;


class SpecificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SpecificationRequest $request)
    {
        //
        $request->validated();
        $specification = Specification::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $specification->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $specification->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $specification = $specification->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($specification,SpecificationResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpecificationStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $specification = specification::create($data);

        Logger::log($specification);

        return ApiResponse::sendResponse(__('messages.specification_create'),new SpecificationResource($specification));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new specification : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.specification_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $specification = Specification::find($id);
        if(!$specification){
            return ApiResponse::sendError(__('messages.specification_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_specification'),new SpecificationResource($specification));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpecificationStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $specification = Specification::findOrFail($id);
            $specification->update($data);
            
        return ApiResponse::sendResponse(__('messages.specification_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating specification : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.specification_update_error'));
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
        $specification = Specification::find($id);
         if(!$specification){
            return ApiResponse::sendError(__('messages.specification_not_found'));
         }
         $specification->delete();
         return ApiResponse::sendResponse(__('messages.specification_delete'));
        }
}
