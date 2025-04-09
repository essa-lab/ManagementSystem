<?php

namespace App\Http\Controllers\Admin\Article;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ScientificBranches\ScientificBranchesRequest;
use App\Http\Requests\Article\ScientificBranches\ScientificBranchesStoreRequest;
use App\Http\Resources\Article\ScientificBranchesResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Article\ScientificBranches;
use Illuminate\Support\Facades\Auth;

class ScientificBranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ScientificBranchesRequest $request)
    {
        //
        $request->validated();
        $scientificBranches = ScientificBranches::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $scientificBranches->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $scientificBranches->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $scientificBranches = $scientificBranches->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($scientificBranches,ScientificBranchesResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ScientificBranchesStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $scientificBranches = ScientificBranches::create($data);

        Logger::log($scientificBranches);

        return ApiResponse::sendResponse(__('messages.scientificBranches_create'),new ScientificBranchesResource($scientificBranches));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new scientificBranches : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.scientificBranches_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $scientificBranches = ScientificBranches::find($id);
        if(!$scientificBranches){
            return ApiResponse::sendError(__('messages.scientificBranches_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_scientificBranches'),new ScientificBranchesResource($scientificBranches));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ScientificBranchesStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $scientificBranches = ScientificBranches::findOrFail($id);
            $scientificBranches->update($data);
            
        return ApiResponse::sendResponse(__('messages.scientificBranches_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating scientificBranches : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.scientificBranches_update_error'));
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
        $scientificBranches = ScientificBranches::find($id);
         if(!$scientificBranches){
            return ApiResponse::sendError(__('messages.scientificBranches_not_found'));
         }
         $scientificBranches->delete();
         return ApiResponse::sendResponse(__('messages.scientificBranches_delete'));
        }
}
