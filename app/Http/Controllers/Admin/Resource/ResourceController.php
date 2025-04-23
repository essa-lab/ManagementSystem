<?php

namespace App\Http\Controllers\Admin\Resource;

use App\Action\GetResourcesAction;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\CuratorStoreRequest;
use App\Http\Requests\Resource\EditorStoreRequst;
use App\Http\Requests\Resource\MediaStoreRequest;
use App\Http\Requests\Resource\ResourcePaginationRequest;
use App\Http\Requests\Resource\ResourceStoreRequest;
use App\Http\Requests\Resource\StoreSourceRequest;
use App\Http\Requests\Resource\StoreSubjectRequest;
use App\Http\Requests\Resource\ViewResourcesRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\ResourceOverviewResource;
use App\Http\Resources\Resource\ResourceResource;
use App\Http\Services\ResourceService;
use App\Models\Article\Article;
use App\Models\Book\Book;
use App\Models\DigitalResource\DigitalResource;
use App\Models\Research\Research;
use App\Models\Resource\Curator;
use App\Models\Resource\Media;
use App\Models\Resource\Resource;
use App\Models\Resource\ResourceCopy;
use App\Models\Resource\ResourceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $mapResources;
    public function __construct()
    {
        $this->mapResources = [
            'Book' => Book::class,
            'Research' => Research::class,
            'Article' => Article::class,
            'DigitalResource' => DigitalResource::class
        ];
    }

    public function index(ResourcePaginationRequest $request, GetResourcesAction $action)
    {
        $request->validated();
        $resources = $action->execute($request);

        return ApiResponse::sendPaginatedResponse(new PaginatingResource($resources, ResourceResource::class));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResourceStoreRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user) && $user->library_id != $request->library_id) {
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $resourceable = $this->mapResources[$data['resourceable_type']];
        $createdResource = $resourceable::create([]);
        DB::beginTransaction();
        try {
            $data['resourceable_id'] = $createdResource->id;
            $data['resourceable_type'] = $resourceable;
            $uuid = (string) Str::orderedUuid();
            $data['uuid'] = $uuid;
            $data['link'] = '/resources/' . $uuid;
            $data['created_by'] = $user->id;
            $resource = Resource::create($data);

            $copies = [];

            for ($i = 1; $i <= $resource->number_of_copies; $i++) {
                $copies[] = [
                    'resource_id' => $resource->id,
                    'copy_number' => $i,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            ResourceCopy::insert($copies);
            ResourceSetting::create([
                'resource_id' => $resource->id
            ]);

            DB::commit();

            return ApiResponse::sendResponse(__('messages.resource_create'), new ResourceResource($resource));
        } catch (\Exception $e) {
            Logger::log('Error Creating new resource : ' . $e->getMessage());
            DB::rollBack();
            return ApiResponse::sendError(__('messages.resource_create_error'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {

        $resource = Resource::find($id);

        if (!$resource) {
            return ApiResponse::sendError(__('messages.Resource_not_found'));
        }

        $resource['related_resources'] = $resource->relatedBySubject();

        return ApiResponse::sendResponse(__('messages.get_Resource'), new ResourceResource($resource));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResourceStoreRequest $request, string $id)
    {
        $data = $request->validated();

        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user) && $user->library_id != $request->library_id) {
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        try {
            $data['resourceable_type'] = $this->mapResources[$request->get('resourceable_type')];
            $resource = Resource::find($id);


            if (!$resource) {
                return ApiResponse::sendError(__('messages.resource_not_found'));
            }
            if ($data['resourceable_type'] != $resource->resourceable_type) {
                return ApiResponse::sendError(__('messages.resource_incorrect_type'));
            }

            DB::beginTransaction();
            $resource->update($data);
            DB::commit();
            return ApiResponse::sendResponse(__('messages.resource_update'), new ResourceResource($resource));
        } catch (\Exception $e) {
            Logger::log('Error Creating new resource : ' . $e->getMessage());
            DB::rollBack();

            return ApiResponse::sendError(__('messages.resource_update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resource = Resource::find($id);
        if (!$resource) {
            return ApiResponse::sendError(__('messages.resource_not_found'));
        }
        $resource->delete();
        return ApiResponse::sendResponse(__('messages.resource_delete'));
    }


    public function storeCurators(CuratorStoreRequest $request, string $resourceId ,ResourceService $service)
    {
        $data = $request->validated();
        try {
            $resource = Resource::find($resourceId);
            if (!$resource) {
                return ApiResponse::sendError(__('messages.resource_not_found'));
            }
            DB::beginTransaction();

            $service->attachCuratorToResource($resource,$data);

            DB::commit();

            return ApiResponse::sendResponse(__('messages.resource_update'), $data);
        } catch (\Exception $e) {
            Logger::log('Error Creating new resource : ' . $e->getMessage());
            DB::rollBack();
            return ApiResponse::sendError(__('messages.resource_update_error'));
        }
    }

    public function deleteCurator($id)
    {

        $curator = Curator::find($id);
        if (!$curator) {
            return ApiResponse::sendError(__('messages.curator_not_found'));
        }
        $curator->delete();
        return ApiResponse::sendResponse(__('messages.curator_delete'));
    }

    public function storeMedia(MediaStoreRequest $request, string $resourceId,ResourceService $service)
    {
        $data = $request->validated();
        // Authorize::hasPermission(Auth::user(),'RESOURCES',$data['library_id']);
        try {
            $resource = Resource::find($resourceId);
            if (!$resource) {
                return ApiResponse::sendError(__('messages.resource_not_found'));
            }
            DB::beginTransaction();

            $service->attachMediaToResource($resource,$data);

            DB::commit();

            return ApiResponse::sendResponse(__('messages.resource_update'), $data);
        } catch (\Exception $e) {
            Logger::log('Error Creating new resource : ' . $e->getMessage());
            DB::rollBack();
            return ApiResponse::sendError(__('messages.resource_update_error'));
        }
    }

    public function deleteMedia($id)
    {

        $meida = Media::find($id);
        if (!$meida) {
            return ApiResponse::sendError(__('messages.meida_not_found'));
        }
        $meida->delete();
        return ApiResponse::sendResponse(__('messages.meida_delete'));
    }

    public function storeSubject(StoreSubjectRequest $request, string $resourceId)
    {
        $data = $request->validated();
        try {
            $resource = Resource::find($resourceId);
            if (!$resource) {
                return ApiResponse::sendError(__('messages.resource_not_found'));
            }
            DB::beginTransaction();
            $resource->subjects()->sync($data['subjectId'] ?? []);
            DB::commit();
            return ApiResponse::sendResponse(__('messages.resource_update'), $data);
        } catch (\Exception $e) {
            Logger::log('Error Creating new resource : ' . $e->getMessage());
            DB::rollBack();
            return ApiResponse::sendError(__('messages.resource_update_error'));
        }
    }

    public function storeSource(StoreSourceRequest $request, string $resourceId)
    {
        $data = $request->validated();
        try {
            $resource = Resource::find($resourceId);
            if (!$resource) {
                return ApiResponse::sendError(__('messages.resource_not_found'));
            }
            DB::beginTransaction();
            // $resource->resourceSource()?->delete();
            $resource->resourceSource()->updateOrCreate(['resource_id' => $resource->id], [
                'title_ar' => $data['title_ar'] ?? null,
                'title_ku' => $data['title_ku'] ?? null,
                'title_en' => $data['title_en'] ?? null,
                'source_id' => $data['source_id'],
                'resource_id' => $resource->id,
            ]);


            DB::commit();
            return ApiResponse::sendResponse(__('messages.resource_update'), $data);
        } catch (\Exception $e) {
            Logger::log('Error Creating new resource : ' . $e->getMessage());
            DB::rollBack();
            return ApiResponse::sendError(__('messages.resource_update_error'));
        }
    }

    public function storeEditor(EditorStoreRequst $request, string $resourceId)
    {

        $data = $request->validated();
        try {
            $resource = Resource::find($resourceId);
            if (!$resource) {
                return ApiResponse::sendError(__('messages.resource_not_found'));
            }
            DB::beginTransaction();


            foreach ($data as $editor) {
                $resource->editors()->updateOrCreate([
                    'resource_id' => $resourceId,
                    'type' => $editor['type'],
                    'language' => $editor['language']
                ], $editor);
            }

            DB::commit();
            return ApiResponse::sendResponse(__('messages.resource_update'), $data);
        } catch (\Exception $e) {
            Logger::log('Error Creating new resource : ' . $e->getMessage());
            DB::rollBack();
            return ApiResponse::sendError(__('messages.resource_update_error'));
        }
    }

    public function viewResources(ViewResourcesRequest $request)
    {

        $data = $request->validated();

        $resources = Resource::where('resourceable_type', $this->mapResources[$data['type']])
            ->whereHas('copies', function ($query) use ($data) {
                $query->where('status', $data['status']);
            })

            ->withCount([
                'copies as copies_count' => function ($query) use ($data) {
                    $query->where('status', $data['status']);
                }
            ])
            ->paginate($data['limit'] ?? 10);

        return ApiResponse::sendPaginatedResponse(new PaginatingResource($resources, ResourceOverviewResource::class));
    }

   
}
