<?php
namespace App\Action;

use App\Helper\Authorize;
use App\Http\Requests\Resource\ResourcePaginationRequest;
use App\Models\Article\Article;
use App\Models\Book\Book;
use App\Models\DigitalResource\DigitalResource;
use App\Models\Research\Research;
use App\Models\Resource\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class GetResourcesAction
{
    public function execute(ResourcePaginationRequest $request)
    {
        $query = Resource::query();
        $user = Auth::user();

        $query = $this->applyFilters($query, $request, $user);

        $query->orderBy($request->get('sortBy', 'id'), $request->get('sortOrder', 'asc'));
        return $query->paginate($request->get('limit', 10));
    }

    private function applyFilters(Builder $query, ResourcePaginationRequest $request, $user)
    {
        if (!Authorize::isSuperAdmin($user)) {
            $query->where('library_id', $user->library_id);
        }

        $this->applyLoadRelations($query, $request);
        
        $filters = ['library_id', 'language_id', 'search', 'subjects', 'resourceSource'];

        foreach ($filters as $filter) {
            $this->applySingleFilter($query, $request, $filter);
        }

        $this->applyResourceableFilters($query, $request);
        return $query;
    }

    private function applySingleFilter(Builder $query, ResourcePaginationRequest $request, string $filter)
    {
        if (!$request->filled($filter)) return;

        if ($filter === 'search') {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title_ar', 'like', "%$searchTerm%")
                    ->orWhere('title_en', 'like', "%$searchTerm%")
                    ->orWhere('title_ku', 'like', "%$searchTerm%");
            });
        } elseif (in_array($filter, ['subjects', 'source'])) {
            $query->whereHas($filter, fn ($q) => $q->whereIn('id', $request->get($filter)));
        } else {
            $query->where($filter, $request->get($filter));
        }
    }

    private function applyResourceableFilters(Builder $query, ResourcePaginationRequest $request)
    {
        $resourceableType = $request->get('resourceable_type', '');
        $resourceableFilters = config('resourceabel.searchableFeilds');

        if (!isset($resourceableFilters[$resourceableType])) return;

        [$model, $fields] = $resourceableFilters[$resourceableType];
        $query->where('resourceable_type', $model);

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $query->whereHas('resourceable', fn ($q) => $q->where($field, $request->get($field)));
            }
        }
    }

    private function applyLoadRelations(Builder $query, ResourcePaginationRequest $request)
    {
        if (!$request->filled('loadRelation')) return;

        $requestedRelations = explode(',', $request->get('loadRelation'));

        $commonRelations = [
            'resourceSource', 'subjects', 'language', 'editors', 'medias', 'curators', 'curators.education',
        ];

        $resourceableRelations = config('resourceabel.relations');

        $validRelations = array_intersect($requestedRelations, $commonRelations);

        $resourceableType = $request->get('resourceable_type');
        if ($resourceableType) {
            $validRelations = array_merge($validRelations, array_intersect($requestedRelations, $resourceableRelations[$resourceableType] ?? []));
            $query->with($validRelations);
        } else {
            // If resourceable_type is NULL, determine relations dynamically per record
            $query->with([
                ...$validRelations,
                'resourceable' => function ($q) use ($requestedRelations, $resourceableRelations) {
                    $q->with(function ($subQ) use ($requestedRelations, $resourceableRelations) {
                        foreach ($resourceableRelations as $type => $relations) {
                            $subQ->when(
                                $subQ->getMorphClass() === $type,
                                fn ($sq) => $sq->with(array_intersect($requestedRelations, $relations))
                            );
                        }
                    });
                }
            ]);
            return;
        }

    }
}
