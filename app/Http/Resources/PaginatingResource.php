<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

class PaginatingResource extends ResourceCollection
{
    protected $resourceClass;

    /**
     * PaginatingResource constructor.
     * 
     * @param AbstractPaginator $resource
     * @param string $resourceClass
     */
    public function __construct(AbstractPaginator $resource, string $resourceClass)
    {
        parent::__construct($resource);
        $this->resourceClass = $resourceClass;
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request)
    {
        return [
            'data' => $this->resourceClass::collection($this->collection),
            'meta' => [
                'total' => $this->resource->total(),
                'limit' => $this->resource->perPage(),
                'page' => $this->resource->currentPage(),
                'previous' => $this->resource->currentPage() > 1 ? $this->resource->currentPage() - 1 : null,
                'next' => $this->resource->hasMorePages() ? $this->resource->currentPage() + 1 : null,
                'last' => $this->resource->lastPage(),
            ],
        ];
    }
}
