<?php

namespace App\Http\Controllers\Admin\Resource;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\Review\BanRequest;
use App\Http\Requests\Resource\Review\ReviewRequest;
use App\Http\Requests\Resource\Review\ReviewStoreRequest;
use App\Http\Requests\Resource\Review\ReviewUpdateRequest;
use App\Http\Requests\Resource\Source\SourceRequest;
use App\Http\Requests\Resource\Source\SourceStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\ReviewResource;
use App\Http\Resources\Resource\SourceResource;
use App\Models\Resource\Review;
use App\Models\Resource\Source;
use Illuminate\Support\Facades\Auth;


class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ReviewRequest $request)
    {
        $request->validated();
        $review = Review::query();
        $review->with(['patron']);
        if($request->has('resource_id')){
            $review->where('resource_id',$request->get('resource_id'));
        }
        $review->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $review = $review->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($review,ReviewResource::class));    
    }
    public function banReview(BanRequest $request)
    {
        $request->validated();
        $review = Review::find($request->get('review_id'));
        if(!$review){
            return ApiResponse::sendError(__('messages.review_not_found'));
        }
        $review->available = $request->get('available');
        $review->save();
        return ApiResponse::sendResponse(__('message.review_updated'));    
    }
    /**
     * Store a newly created resource in storage.
     */
    public function showReview(ReviewRequest $request)
    {
        $request->validated();
        $review = Review::query();
        $review->with(['patron']);
        if($request->has('resource_id')){
            $review->where('resource_id',$request->get('resource_id'));
        }
        $review->where('available',1);
        $review->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $review = $review->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($review,ReviewResource::class));    
    }
    public function store(ReviewStoreRequest $request)
    {
        $data = $request->validated();
        $user = auth('patron')->user()->id;
        try{
            $reviewed = Review::where('resource_id',$data['resource_id'])->where('patron_id',$user)->exists();
            if($reviewed){
                return ApiResponse::sendError(__('messages.cant_review_again'));

            }
        $review =Review::create([
            'resource_id'=>$data['resource_id'],
            'patron_id'=>$user,
            'rate'=>$data['rate'],
            'review'=>$data['review'],
        ]);
        return ApiResponse::sendResponse(__('messages.review_create'),new ReviewResource($review));
        }catch(\Exception $e){
            Logger::log('Error Creating new review : '.$e->getMessage());
            return ApiResponse::sendError(__('messages.review_create_error'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $review = Review::with('resource')->where('patron_id',auth('patron')->user()->id)->get();
        return ApiResponse::sendResponse(__('messages.get_source'), ReviewResource::collection($review));
    }

    public function getOneReview($id)
    {
        $review = Review::with('resource')->where('available',1)->find($id);
        if(!$review){
            return ApiResponse::sendError(__('messages.review_not_Found'));

        }
        return ApiResponse::sendResponse(__('messages.get_source'), new ReviewResource($review));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        try{
            $review =Review::findOrFail($id);
            if(auth('patron')->user()->id != $review->patron_id){
                return ApiResponse::sendError(__('messages.not_your_review'));

            }
            $review->update($data);
        return ApiResponse::sendResponse(__('messages.review_update'));
        }catch(\Exception $e){
            Logger::log('Error updating review : '.$e->getMessage());
            return ApiResponse::sendError(__('messages.review_update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::find($id);
         if(!$review){
            return ApiResponse::sendError(__('messages.review_not_found'));
         }
         $review->delete();
         return ApiResponse::sendResponse(__('messages.review_delete'));
        }
}
