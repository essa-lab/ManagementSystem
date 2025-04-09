<?php

namespace App\Http\Controllers\Patron;

use App\Helper\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\Library\LibraryRequest;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\ResourceCounterLibraryResource;
use App\Http\Resources\Resource\ResourceCounterResource;
use App\Models\Library;
use App\Models\Resource\Resource;
use Illuminate\Http\Request;

class PatronController extends Controller
{

    public function resourceCount(){  
        $resourceCounts = Resource::select('library_id')->with('library')
        ->selectRaw('COUNT(*) as total_resources')
        ->groupBy('library_id')
        ->get();

        return ApiResponse::sendResponse('success',ResourceCounterLibraryResource::collection($resourceCounts));
    }

    public function libraryList(LibraryRequest $request)
    {
        $request->validated();
        $library = Library::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $library->where(function($query) use ($searchTerm) {
                $query->where('name_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('name_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('name_ku', 'like', '%'.$searchTerm.'%');
            });
        }
        $library->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $library = $library->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($library,LibraryResource::class));       
    }
    
}
