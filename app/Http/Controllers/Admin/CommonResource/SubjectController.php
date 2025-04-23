<?php

namespace App\Http\Controllers\Admin\CommonResource;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\Subject\SubjectRequest;
use App\Http\Requests\Resource\Subject\SubjectStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\SubjectResource;
use App\Models\Resource\Subject;
use Illuminate\Support\Facades\Auth;


class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SubjectRequest $request)
    {
        //
        $request->validated();
        $subject = Subject::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $subject->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }


        $subject->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $subject = $subject->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($subject,SubjectResource::class));    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubjectStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $subject =Subject::create($data);

        Logger::log($subject);

        return ApiResponse::sendResponse(__('messages.subject_create'),new SubjectResource($subject));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new subject : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.subject_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $subject = Subject::find($id);
        if(!$subject){
            return ApiResponse::sendError(__('messages.subject_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_subject'),new SubjectResource($subject));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubjectStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $subject =Subject::findOrFail($id);
            $subject->update($data);
        return ApiResponse::sendResponse(__('messages.subject_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating subject : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.subject_update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $subject = Subject::find($id);
         if(!$subject){
            return ApiResponse::sendError(__('messages.subject_not_found'));
         }
         $subject->delete();
         return ApiResponse::sendResponse(__('messages.subject_delete'));
        }
}
