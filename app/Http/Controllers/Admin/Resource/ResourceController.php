<?php

namespace App\Http\Controllers\Admin\Resource;
use App\Action\GetResourcesAction;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\AuthorStoreRequest;
use App\Http\Requests\Resource\CuratorStoreRequest;
use App\Http\Requests\Resource\EditorStoreRequst;
use App\Http\Requests\Resource\MediaStoreRequest;
use App\Http\Requests\Resource\ResourcePaginationRequest;
use App\Http\Requests\Resource\ResourceStoreRequest;
use App\Http\Requests\Resource\StoreSourceRequest;
use App\Http\Requests\Resource\StoreSubjectRequest;
use App\Http\Requests\Resource\TopTenRequest;
use App\Http\Requests\Resource\ViewResourcesRequest;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\LanguageResource;
use App\Http\Resources\Resource\ResourceOverviewResource;
use App\Http\Resources\Resource\ResourceResource;
use App\Http\Resources\Resource\SubjectResource;
use App\Models\Article\Article;
use App\Models\Book\Book;
use App\Models\DigitalResource\DigitalResource;
use App\Models\Library;
use App\Models\Research\Research;
use App\Models\Resource\Circulation;
use App\Models\Resource\Curator;
use App\Models\Resource\Media;
use App\Models\Resource\Resource;
use App\Models\Resource\ResourceCopy;
use App\Models\Resource\ResourceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    public function resourceCountPerLibraryAndLanguage()
    {
        $user = Auth::user();
        $libraryId = null;
        if (!Authorize::isSuperAdmin($user)) {
            $libraryId = $user->library_id;
        }

        $resourceCounts = Resource::query();

        if (isset($libraryId)) {
            $resourceCounts->where('library_id', $libraryId);
        }

        $resourceCounts = $resourceCounts->select('library_id', 'language_id')
            ->with(['library', 'language'])
            ->selectRaw("
            SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as book_count,
            SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as article_count,
            SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as research_count,
            SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as digital_count,
            SUM(
                CASE WHEN resourceable_type IN (?, ?, ?, ?) THEN 1 ELSE 0 END
            ) as total_count
        ", [
                'App\Models\Book\Book',
                'App\Models\Article\Article',
                'App\Models\Research\Research',
                'App\Models\DigitalResource\DigitalResource',
                'App\Models\Book\Book',
                'App\Models\Article\Article',
                'App\Models\Research\Research',
                'App\Models\DigitalResource\DigitalResource'
            ])
            ->groupBy('library_id', 'language_id')
            ->get()
            ->groupBy('library_id')
            ->map(function ($items, $libraryId) {
                return [
                    'library_id' => $libraryId,
                    'library' => new LibraryResource($items->first()->library),
                    'details' => $items->map(function ($item) {
                        return [
                            'language_id' => $item->language_id,
                            'language' => new LanguageResource($item->language),
                            'book_count' => (int) $item->book_count,
                            'article_count' => (int) $item->article_count,
                            'research_count' => (int) $item->research_count,
                            'digital_count' => (int) $item->digital_count,
                            'total_count' => (int) $item->total_count,
                        ];
                    })->values()
                ];
            })->values();



        return ApiResponse::sendResponse('success', $resourceCounts);
    }

    public function resourceCountPerLibrary()
    {
        $user = Auth::user();
        $libraryId = null;
        if (!Authorize::isSuperAdmin($user)) {
            $libraryId = $user->library_id;
        }

        $resourceCounts = Resource::query();

        if (isset($libraryId)) {
            $resourceCounts->where('library_id', $libraryId);
        }

        $resourceCounts = $resourceCounts->select('library_id')
            ->with(['library'])
            ->selectRaw("SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as book_count", ['App\Models\Book\Book'])
            ->selectRaw("SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as article_count", ['App\Models\Article\Article'])
            ->selectRaw("SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as research_count", ['App\Models\Research\Research'])
            ->selectRaw("SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as digital_count", ['App\Models\DigitalResource\DigitalResource'])
            ->groupBy('library_id')
            ->get();

        return ApiResponse::sendResponse('success', $resourceCounts);   
     }

    public function resourceCountPerSubject()
    {
        $user = Auth::user();

        


$libraries = Cache::remember('library_subjects_' . $user->id, now()->addHour(), function () use ($user) {
    return Library::query()
        ->when(!Authorize::isSuperAdmin($user), function ($query) use ($user) {
            $query->where('id', $user->library_id);
        })
        ->with(['resources.subjects'])
        ->get()
        ->map(function ($library) {
            return [
                'library' => new LibraryResource($library),
                'subjects' => $library->resources->flatMap->subjects
                    ->groupBy('id')
                    ->map(fn ($subjects) => [
                        'subject' => new SubjectResource($subjects->first()),
                        'total_resources' => count($subjects)
                    ])
                    ->values()
            ];
        });
});

        return ApiResponse::sendResponse('success', $libraries);
    }

    // public function storeCurator(CuratorStoreRequest $request, string $resourceId)
    // {
    //     $data = $request->validated();
    //     // Authorize::hasPermission(Auth::user(),'RESOURCES',$data['library_id']);
    //     try {
    //         $resource = Resource::find($resourceId);
    //         if (!$resource) {
    //             return ApiResponse::sendError(__('messages.resource_not_found'));
    //         }
    //         if (empty($data)) {
    //             $resource->curators()?->where('type', '!=', 'author')->delete();
    //         }
    //         DB::beginTransaction();
    //         foreach ($data as $curator) {
    //             $resource->curators()?->where('type', $curator['type'])->delete();
    //         }
    //         $curators = array_map(fn($curator) => [
    //             'name_ar' => $curator['name_ar'] ?? null,
    //             'name_ku' => $curator['name_ku'] ?? null,
    //             'name_en' => $curator['name_en'] ?? null,
    //             'type' => $curator['type'],
    //             'resource_id' => $resource->id,
    //         ], $data);

    //         $createdCurators = $resource->curators()->createMany($curators);

    //         if ($resource->resourceable_type == Research::class || $resource->resourceable_type == Article::class) {

    //             foreach ($createdCurators as $index => $curator) {
    //                 if (!empty($data[$index]['education_level'])) {
    //                     $curator->education()?->delete();
    //                     $curator->education()->create($data[$index]['education_level']);
    //                 }
    //             }
    //         }
    //         DB::commit();
    //         return ApiResponse::sendResponse(__('messages.resource_update'), $data);
    //     } catch (\Exception $e) {
    //         Logger::log('Error Creating new resource : ' . $e->getMessage());
    //         DB::rollBack();
    //         return ApiResponse::sendError(__('messages.resource_update_error'));
    //     }
    // }
    // public function storeAuthor(AuthorStoreRequest $request, string $resourceId)
    // {
    //     $data = $request->validated();
    //     // Authorize::hasPermission(Auth::user(),'RESOURCES',$data['library_id']);
    //     try {
    //         $resource = Resource::find($resourceId);
    //         if (!$resource) {
    //             return ApiResponse::sendError(__('messages.resource_not_found'));
    //         }
    //         DB::beginTransaction();

    //         $existingCurators = $resource->curators()->get()->keyBy('id');

    //         $curatorsToInsert = [];

    //         foreach ($data as $curatorData) {
    //             $curatorId = $curatorData['id'] ?? null; 

    //             $curatorFields = [
    //                 'name_ar' => $curatorData['name_ar'] ?? null,
    //                 'name_ku' => $curatorData['name_ku'] ?? null,
    //                 'name_en' => $curatorData['name_en'] ?? null,
    //                 'type' => $curatorData['type'],
    //                 'resource_id' => $resource->id,
    //             ];
    //             if ($curatorId && isset($existingCurators[$curatorId])) {
    //                 $existingCurators[$curatorId]->update($curatorFields);
    //             } else {
    //                 $curatorsToInsert[] = $curatorFields;
    //             }
    //         }

    //         if (!empty($curatorsToInsert)) {
    //             $resource->curators()->insert($curatorsToInsert);
    //         }

    //         if ($resource->resourceable_type == Research::class || $resource->resourceable_type == Article::class) {
    //             foreach ($data as $curatorData) {
    //                 if (!empty($curatorData['id']) && !empty($curatorData['education_level'])) {
    //                     $curator = $resource->curators()->find($curatorData['id']);
    //                     if ($curator) {
    //                         $curator->education()?->updateOrCreate([], $curatorData['education_level']);
    //                     }
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         return ApiResponse::sendResponse(__('messages.resource_update'), $data);
    //     } catch (\Exception $e) {
    //         Logger::log('Error Creating new resource : ' . $e->getMessage());
    //         DB::rollBack();
    //         return ApiResponse::sendError(__('messages.resource_update_error'));
    //     }
    // }



    public function storeCurators(CuratorStoreRequest $request, string $resourceId)
    {
        $data = $request->validated();
        // Authorize::hasPermission(Auth::user(),'RESOURCES',$data['library_id']);
        try {
            $resource = Resource::find($resourceId);
            if (!$resource) {
                return ApiResponse::sendError(__('messages.resource_not_found'));
            }
            DB::beginTransaction();

            $existingCurators = $resource->curators()->get()->keyBy('id');

            $curatorsToInsert = [];

            foreach ($data as $curatorData) {
                $curatorId = $curatorData['id'] ?? null;

                $curatorFields = [
                    'name_ar' => $curatorData['name_ar'] ?? null,
                    'name_ku' => $curatorData['name_ku'] ?? null,
                    'name_en' => $curatorData['name_en'] ?? null,
                    'type' => $curatorData['type'],
                    'resource_id' => $resource->id,
                ];
                if ($curatorId && isset($existingCurators[$curatorId])) {
                    $existingCurators[$curatorId]->update($curatorFields);
                } else {
                    $curatorsToInsert[] = $curatorFields;
                }
            }
            if (!empty($curatorsToInsert)) {
                $resource->curators()->insert($curatorsToInsert);
            
                $resource->load('curators'); 
                $insertedCurators = $resource->curators;
            }
            
            if ($resource->resourceable_type == Research::class || $resource->resourceable_type == Article::class) {
                foreach ($data as $curatorData) {
                    if (isset($curatorData['id']) && isset($curatorData['education_level'])) {
                        info('entered');
                        $curator = $resource->curators()->find($curatorData['id']);
                        if ($curator) {
                            $curator->education()?->updateOrCreate([], $curatorData['education_level']);
                        }
                    } elseif (isset($curatorData['education_level'])) {
                        $newCurator = $insertedCurators->first(function ($curator) use ($curatorData) {
                            return ($curator->name_ar === ($curatorData['name_ar'] ?? null)) ||
                                   ($curator->name_ku === ($curatorData['name_ku'] ?? null)) ||
                                   ($curator->name_en === ($curatorData['name_en'] ?? null));
                        });
            
                        if ($newCurator) {
                            $newCurator->education()->create($curatorData['education_level']);
                        }
                    }
                }
            }

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

    public function storeMedia(MediaStoreRequest $request, string $resourceId)
    {
        $data = $request->validated();
        // Authorize::hasPermission(Auth::user(),'RESOURCES',$data['library_id']);
        try {
            $resource = Resource::find($resourceId);
            if (!$resource) {
                return ApiResponse::sendError(__('messages.resource_not_found'));
            }
            DB::beginTransaction();

            $existingMedias = $resource->medias()->get()->keyBy('id');

            $mediasToInsert = [];

            foreach ($data as $mediaData) {
                $mediaId = $mediaData['id'] ?? null;

                $mediaFields = [
                    'path' => $mediaData['path'],
                    'type' => $mediaData['type'],
                    'resource_id' => $resource->id,
                ];
                if ($mediaId && isset($existingMedias[$mediaId])) {
                    $existingMedias[$mediaId]->update($mediaFields);
                } else {
                    $mediasToInsert[] = $mediaFields;
                }
            }

            if (!empty($mediasToInsert)) {
                $resource->medias()->insert($mediasToInsert);
            }

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


    // public function storeMedia(MediaStoreRequest $request, string $resourceId)
    // {

    //     $data = $request->validated();
    //     try {
    //         $resource = Resource::find($resourceId);
    //         if (!$resource) {
    //             return ApiResponse::sendError(__('messages.resource_not_found'));
    //         }
    //         DB::beginTransaction();
    //         foreach ($data as $media) {
    //             $resource->medias()?->where('type', $media['type'])->delete();
    //         }
    //         foreach ($data as $media) {
    //             $media['resource_id'] = $resource->id;
    //             $resource->medias()->create($media);
    //         }
    //         DB::commit();
    //         return ApiResponse::sendResponse(__('messages.resource_update'), $data);
    //     } catch (\Exception $e) {
    //         Logger::log('Error Creating new resource : ' . $e->getMessage());
    //         DB::rollBack();
    //         return ApiResponse::sendError(__('messages.resource_update_error'));
    //     }

    // }


    public function storeSubject(StoreSubjectRequest $request, string $resourceId)
    {
        $data = $request->validated();
        info($data);
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

            // foreach ($data as $editor) {
            //     $resource->editors()?->where('type', $editor['type'])->delete();
            // }

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

    public function topTen(TopTenRequest $request)
    {

        $data = $request->validated();

        $circulations = Circulation::select(
            'resources.title_en',
            'resources.title_ku',
            'resources.title_ar',
            'resources.resourceable_type',
            DB::raw('COUNT(circulations.id) as circulation_count')
        )
            ->join('resource_copies', 'circulations.resource_copy_id', '=', 'resource_copies.id')
            ->join('resources', 'resource_copies.resource_id', '=', 'resources.id')
            ->where('resources.resourceable_type', $this->mapResources[$data['type']])
            ->groupBy('resources.id', 'resources.title_en', 'resources.title_ku', 'resources.title_ar', 'resources.resourceable_type')
            ->orderByDesc('circulation_count')
            ->limit(10)
            ->get();

        return ApiResponse::sendResponse(__('messages.resource_update'), $circulations);

    }


}
