<?php

namespace App\Http\Controllers\Admin\Resource;
use App\Helper\ApiResponse;
use App\Helper\GenerateBarcode;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\ResourceCopy\ResourceCopyRequest;
use App\Http\Requests\Resource\ResourceCopy\ResourceCopyStoreRequest;
use App\Http\Requests\Resource\ResourceCopy\ResourceCopyUpdateRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\ResourceCopyResource;
use App\Http\Resources\Resource\ResourceResource;
use App\Models\Resource\Resource;
use App\Models\Resource\ResourceCopy;
use Barryvdh\DomPDF\Facade\Pdf;

class ResourceCopyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResourceCopyRequest $request)
    {
        //
        $request->validated();
        $resourceCopy = ResourceCopy::query();

        if($request->has('loadRelation')){
            $resourceCopy->with('resource');
        }
        if($request->has('status')){
            $resourceCopy->where('status',$request->get('status'));
        }
        
        if($request->has('resource_id')){
            $resourceCopy->where('resource_id',$request->get('resource_id'));
        }


        $resourceCopy->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $resourceCopy = $resourceCopy->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($resourceCopy,ResourceCopyResource::class));    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResourceCopyStoreRequest $request)
    {
        
        $data = $request->validated();
        try{


        $resourceCopy =ResourceCopy::create($data);


        return ApiResponse::sendResponse(__('messages.resourceCopy_create'),new ResourceCopyResource($resourceCopy));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new resourceCopy : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.resourceCopy_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $resourceCopy = ResourceCopy::find($id);
        if(!$resourceCopy){
            return ApiResponse::sendError(__('messages.resourceCopy_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_resourceCopy'),new ResourceCopyResource($resourceCopy));
    }
    public function showCopyForResource(string $id)
    {
        //

        $resourceCopy = ResourceCopy::where('resource_id',$id)->get();
        $resource = Resource::find($id);
        if(!$resourceCopy || !$resource){
            return ApiResponse::sendError(__('messages.resourceCopy_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_resourceCopy'), ['resource'=>new ResourceResource($resource),'copies'=> ResourceCopyResource::collection($resourceCopy)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResourceCopyUpdateRequest $request, string $id)
    {

        $data = $request->validated();
        try{

            $resourceCopy =ResourceCopy::find($id);
            if(!$resourceCopy){
                return ApiResponse::sendError(__('messages.resourceCopy_not_found'));

            }
            
            $resourceCopy->update($data);
        return ApiResponse::sendResponse(__('messages.resourceCopy_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating resourceCopy : '.$e->getMessage());

            return ApiResponse::sendError($e->getMessage());
        }
    }

    public function getBarcode($id){

        try{

            $resourceCopy =ResourceCopy::find($id);
            if(!$resourceCopy){
                return ApiResponse::sendError(__('messages.resourceCopy_not_found'));

            }
            $barcode = GenerateBarcode::generateBarcode($resourceCopy->barcode);
            $pdf = Pdf::loadView('barcode', compact('barcode'));
                return response()->streamDownload(
                    fn() => print ($pdf->output()),
                    'barcode.pdf',
                    ['Content-Type' => 'application/pdf']
                );

                
            
        
        }catch(\Exception $e){
            Logger::log('Error updating resourceCopy : '.$e->getMessage());

            return ApiResponse::sendError($e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $resourceCopy = ResourceCopy::find($id);
         if(!$resourceCopy){
            return ApiResponse::sendError(__('messages.resourceCopy_not_found'));
         }
         $resourceCopy->delete();
         return ApiResponse::sendResponse(__('messages.resourceCopy_delete'));
        }
}
