<?php

namespace App\Http\Controllers\Admin\DigitalResource;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Books\Book\BookRequest;
use App\Http\Requests\DigitalResource\DigitalResource\DigitalRelationRequest;
use App\Http\Requests\DigitalResource\DigitalResource\DigitalRightRequest;
use App\Http\Requests\DigitalResource\DigitalResource\DigitalSpecificSubjectRequest;
use App\Http\Requests\Resource\ResourceRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\ResourceResource;
use App\Models\DigitalResource\DigitalResource;
use App\Http\Requests\DigitalResource\DigitalResource\DigitalResourceStoreRequest;
use App\Models\Resource\Resource;
use Illuminate\Support\Facades\Auth;



class DigitalResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResourceRequest $request)
    {
        //
        $request->validated();
        $digitalResource = Resource::query();
        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user)) {
            $digitalResource->where('library_id', $user->library_id);
        }
        $digitalResource->where('resourceable_type',DigitalResource::class);
        if($request->has('title')){
            $searchTerm = $request->get('title');
            $digitalResource->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });      
        }
        if($request->has('language_id')){
            $digitalResource->where('language_id',$request->get('language_id'));
        }
        if ($request->filled('identifier')) {
                $registrationNumber = $request->get('identifier');
                $digitalResource->whereHasMorph('resourceable', [DigitalResource::class], function ($query) use ($registrationNumber) {
                    $query->where('identifier', $registrationNumber);
                });
            
        }
        $digitalResource->with(['library','language','resourceSource.source','subjects','curators']);
        $digitalResource->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $digitalResource = $digitalResource->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($digitalResource, ResourceResource::class));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DigitalResourceStoreRequest $request)
    {
        $data = $request->validated();
        try{

            unset($data['digital_resource_id']);
            $digitalResource = DigitalResource::find($request->digital_resource_id);
            if(!$digitalResource){
                return ApiResponse::sendError(__('messages.digitalResource_create_error'));
            }
            $digitalResource->update($data);

        return ApiResponse::sendResponse(__('messages.DigitalResource_create'),DigitalResource::find($request->digital_resource_id));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new DigitalResource : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.DigitalResource_create_error'));

        }
    }
    public function storeRelations(DigitalRelationRequest $request, string $digitalResourceId)
    {
        $data = $request->validated();
        try {
            $digitalResource = DigitalResource::find($digitalResourceId);
            if (!$digitalResource) {
                return ApiResponse::sendError(__('messages.digitalResource_not_found'));
            }

            $digitalRelation = array_map(function ($relation) use ($digitalResource) {
                return [
                    'title_ar' => $relation['title_ar'] ?? null,
                    'title_ku' => $relation['title_ku'] ?? null,
                    'title_en' => $relation['title_en'] ?? null,
                    'digital_resource_id' => $digitalResource->id,
                ];
            }, $data);
            
            $digitalResource->relations()?->delete();
            $digitalResource->relations()->insert($digitalRelation);

            return ApiResponse::sendResponse(__('messages.digitalResource_create'), $digitalRelation);

        } catch (\Exception $e) {
            Logger::log('Error Creating new digitalResource : ' . $e->getMessage());

            return ApiResponse::sendError(__('messages.digitalResource_create_error'));

        }
    }
    public function storeRight(DigitalRightRequest $request, string $digitalResourceId)
    {
        $data = $request->validated();
        try {
            $digitalResource = DigitalResource::find($digitalResourceId);
            if (!$digitalResource) {
                return ApiResponse::sendError(__('messages.digitalResource_not_found'));
            }

            $data['digital_resource_id']=$digitalResourceId;
            
            $digitalResource->right()?->delete();
            $digitalResource->right()->create($data);

            return ApiResponse::sendResponse(__('messages.digitalResource_create'), $data);

        } catch (\Exception $e) {
            Logger::log('Error Creating new digitalResource : ' . $e->getMessage());

            return ApiResponse::sendError(__('messages.digitalResource_create_error'));

        }
    }
    public function storeSpecificSubject(DigitalSpecificSubjectRequest $request, string $digitalResourceId)
    {
        $data = $request->validated();
        try {
            $digitalResource = DigitalResource::find($digitalResourceId);
            if (!$digitalResource) {
                return ApiResponse::sendError(__('messages.digitalResource_not_found'));
            }

            $data['digital_resource_id']=$digitalResourceId;

            $digitalResource->specificSubject()?->delete();
            $digitalResource->specificSubject()->create($data);

            return ApiResponse::sendResponse(__('messages.digitalResource_create'), $data);

        } catch (\Exception $e) {
            Logger::log('Error Creating new digitalResource : ' . $e->getMessage());

            return ApiResponse::sendError(__('messages.digitalResource_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
//
        $digitalResource = Resource::with(['library', 'language', 'curators', 'editors', 'resourceSource.source', 'subjects', 'medias'])->find($id);
        if (!$digitalResource) {
            return ApiResponse::sendError(__('messages.digitalResource_not_found'));
        }
        if ($digitalResource->resourceable_type != DigitalResource::class) {
            return ApiResponse::sendError(__('messages.uncompatible_resourceable_type'));

        }

        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user) && $user->library_id != $digitalResource->library_id) {
            return ApiResponse::sendError(__('messages.not_authorized'));
        }

        $digitalResource->load(config('resourceabel.relations.digital_resource'));
        $digitalResource['related_resources'] = $digitalResource->relatedBySubject();



        return ApiResponse::sendResponse(__('messages.get_digitalResource'), new ResourceResource($digitalResource));
    }
}
