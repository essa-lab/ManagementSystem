<?php

namespace App\Http\Services;

use App\Models\Article\Article;
use App\Models\Research\Research;
use App\Models\Resource\Resource;

class ResourceService{

    public function attachCuratorToResource(Resource $resource, $data) : void {
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
    }

    public function attachMediaToResource(Resource $resource, $data): void{
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
    }
}