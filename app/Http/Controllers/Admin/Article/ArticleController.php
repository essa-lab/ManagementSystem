<?php

namespace App\Http\Controllers\Admin\Article;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\Article\ArticleKeywordRequest;
use App\Http\Requests\Article\Article\ArticleStoreRequest;
use App\Http\Requests\Resource\ResourceRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\ResourceResource;
use App\Models\Article\Article;
use App\Models\Article\ArticleKeyword;
use App\Models\Resource\Resource;
use Illuminate\Support\Facades\Auth;



class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResourceRequest $request)
    {
        //
        $request->validated();
        $article = Resource::query();
        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user)) {
            $article->where('library_id', $user->library_id);
        }
        $article->where('resourceable_type',Article::class);
        if($request->has('title')){
            $searchTerm = $request->get('title');
            $article->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });      
        }
        if($request->has('language_id')){
            $article->where('language_id',$request->get('language_id'));
        }
        if ($request->filled('registeration_number')) {
            $registrationNumber = $request->get('registeration_number');
            $article->whereHasMorph('resourceable', [Article::class], function ($query) use ($registrationNumber) {
                $query->where('registeration_number', $registrationNumber);
            });
        }

        $article->with(['library','language','resourceSource.source','subjects','curators']);
        $article->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $article = $article->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($article, ResourceResource::class));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleStoreRequest $request)
    {
        $data = $request->validated();
        
        try {
            unset($data['article_id']);
            $article = Article::find($request->article_id);
            if(!$article){
                return ApiResponse::sendError(__('messages.article_create_error'));
            }
            $article->update($data);

            return ApiResponse::sendResponse(__('messages.article_create'), Article::find($request->article_id));

        } catch (\Exception $e) {
            Logger::log('Error Creating new article : ' . $e->getMessage());

            return ApiResponse::sendError(__('messages.article_create_error'));

        }
    }
    // public function storeKeywords(ArticleKeywordRequest $request, string $articleId)
    // {
    //     $data = $request->validated();
    //     try {
    //         $article = Article::find($articleId);
    //         if (!$article) {
    //             return ApiResponse::sendError(__('messages.article_not_found'));
    //         }

    //         $articleKeywords = array_map(function ($relation) use ($article) {
    //             return [
    //                 'title_ar' => $relation['title_ar'] ?? null,
    //                 'title_ku' => $relation['title_ku'] ?? null,
    //                 'title_en' => $relation['title_en'] ?? null,
    //                 'article_id' => $article->id,
    //             ];
    //         }, $data);
    //         $article->articleKeyword()?->delete();
    //         $article->articleKeyword()->insert($articleKeywords);

    //         return ApiResponse::sendResponse(__('messages.article_create'), $articleKeywords);

    //     } catch (\Exception $e) {
    //         Logger::log('Error Creating new article : ' . $e->getMessage());

    //         return ApiResponse::sendError(__('messages.article_create_error'));

    //     }
    // }

    public function storeKeywords(ArticleKeywordRequest $request, string $articleId)
    {
        $data = $request->validated();
        try {
            $article = Article::find($articleId);
            if (!$article) {
                return ApiResponse::sendError(__('messages.article_not_found'));
            }

            $existingKeyword = $article->articleKeyword()->get()->keyBy('id');

            $keywordToInsert = [];

            foreach ($data as $keywordData) {
                $keywordId = $keywordData['id'] ?? null; 

                $keywordFeilds = [
                    'title_ar' => $keywordData['title_ar'] ?? null,
                    'title_ku' => $keywordData['title_ku'] ?? null,
                    'title_en' => $keywordData['title_en'] ?? null,
                    'article_id' => $article->id,
                ];
                if ($keywordId && isset($existingKeyword[$keywordId])) {
                    $existingKeyword[$keywordId]->update($keywordFeilds);
                } else {
                    $keywordToInsert[] = $keywordFeilds;
                }
            }

            if (!empty($keywordToInsert)) {
                $article->articleKeyword()->insert($keywordToInsert);
            }


            return ApiResponse::sendResponse(__('messages.article_create'), $data);

        } catch (\Exception $e) {
            Logger::log('Error Creating new article : ' . $e->getMessage());

            return ApiResponse::sendError(__('messages.article_create_error'));

        }
    }

    public function deleteArticleKeyword($id){
        
        $keyword = ArticleKeyword::find($id);
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
        $article = Resource::with(['library','language','curators','editors','resourceSource.source','subjects','medias','curators.education'])->find($id);
        if(!$article){
            return ApiResponse::sendError(__('messages.article_not_found'));
        }
        if($article->resourceable_type != Article::class){
            return ApiResponse::sendError(__('messages.uncompatible_resourceable_type'));

        }

        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user) && $user->library_id != $article->library_id) {
            return ApiResponse::sendError(__('messages.not_authorized'));
        }

        $article->load(config('resourceabel.relations.article'));
        $article['related_resources']=$article->relatedBySubject();

        
        
        return ApiResponse::sendResponse(__('messages.get_article'),new ResourceResource($article));
    }

}
