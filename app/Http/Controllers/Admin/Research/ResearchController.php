<?php

namespace App\Http\Controllers\Admin\Research;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Books\Book\BookRequest;
use App\Http\Requests\Research\Research\ResearchKeywordRequest;
use App\Http\Requests\Research\Research\ResearchStoreRequest;
use App\Http\Requests\Resource\ResourceRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\ResourceResource;
use App\Models\Research\Research;
use App\Models\Research\ResearchKeyword;
use App\Models\Resource\Resource;
use Illuminate\Support\Facades\Auth;




class ResearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResourceRequest $request)
    {
        //
        $request->validated();
        $research = Resource::query();
        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user)) {
            $research->where('library_id', $user->library_id);
        }
        $research->where('resourceable_type',Research::class);
        if($request->has('title')){
            $searchTerm = $request->get('title');
            $research->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });      
        }
        if($request->has('language_id')){
            $research->where('language_id',$request->get('language_id'));
        }
        if ($request->filled('registeration_number')) {
            $registrationNumber = $request->get('registeration_number');
            $research->whereHasMorph('resourceable', [Research::class], function ($query) use ($registrationNumber) {
                $query->where('registeration_number', $registrationNumber);
            });
        }
        $research->with(['library','language','resourceSource.source','subjects','curators']);
        $research->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $research = $research->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($research, ResourceResource::class));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResearchStoreRequest $request)
    {
        $data = $request->validated();
        try{
            unset($data['research_id']);
            $research = Research::find($request->research_id);
            if(!$research){
                return ApiResponse::sendError(__('messages.research_create_error'));
            }
            $research->update($data);


        return ApiResponse::sendResponse(__('messages.research_create'),Research::find($request->research_id));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new article : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.research_create_error'));

        }
    }
    public function storeKeywords(ResearchKeywordRequest $request, string $researchId)
    {
        $data = $request->validated();
        try {
            $research = Research::find($researchId);
            if (!$research) {
                return ApiResponse::sendError(__('messages.research_not_found'));
            }

            $existingKeyword = $research->researchKeywords()->get()->keyBy('id');

            $keywordToInsert = [];

            foreach ($data as $keywordData) {
                $keywordId = $keywordData['id'] ?? null; 

                $keywordFeilds = [
                    'title_ar' => $keywordData['title_ar'] ?? null,
                    'title_ku' => $keywordData['title_ku'] ?? null,
                    'title_en' => $keywordData['title_en'] ?? null,
                    'research_id' => $research->id,
                ];
                if ($keywordId && isset($existingKeyword[$keywordId])) {
                    $existingKeyword[$keywordId]->update($keywordFeilds);
                } else {
                    $keywordToInsert[] = $keywordFeilds;
                }
            }

            if (!empty($keywordToInsert)) {
                $research->researchKeywords()->insert($keywordToInsert);
            }


            return ApiResponse::sendResponse(__('messages.research_create'), $data);

        } catch (\Exception $e) {
            Logger::log('Error Creating new research : ' . $e->getMessage());

            return ApiResponse::sendError(__('messages.research_create_error'));

        }
    }

    public function deleteResearchKeyword($id){
        
        $keyword = ResearchKeyword::find($id);
         if(!$keyword){
            return ApiResponse::sendError(__('messages.keyword_not_found'));
         }
         $keyword->delete();
         return ApiResponse::sendResponse(__('messages.keyword_delete'));
        
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
//
        $research = Resource::with(['library', 'language', 'curators', 'editors', 'resourceSource.source', 'subjects', 'medias','curators.education'])->find($id);
        if (!$research) {
            return ApiResponse::sendError(__('messages.research_not_found'));
        }
        if ($research->resourceable_type != Research::class) {
            return ApiResponse::sendError(__('messages.uncompatible_resourceable_type'));

        }

        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user) && $user->library_id != $research->library_id) {
            return ApiResponse::sendError(__('messages.not_authorized'));
        }

        $research->load(config('resourceabel.relations.research'));
        $research['related_resources'] = $research->relatedBySubject();



        return ApiResponse::sendResponse(__('messages.get_research'), new ResourceResource($research));
    }
}
