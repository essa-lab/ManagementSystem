<?php

namespace App\Http\Controllers\Admin\Book;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Books\TranslationType\TranslationTypeRequest;
use App\Http\Requests\Books\TranslationType\TranslationTypeStoreRequest;
use App\Http\Resources\Book\TranslationTypeResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Book\TranslationType;
use Illuminate\Support\Facades\Auth;


class TranslationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TranslationTypeRequest $request)
    {
        //
        $request->validated();
        $translationType = TranslationType::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $translationType->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $translationType->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $translationType = $translationType->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($translationType,TranslationTypeResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TranslationTypeStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $translationType = TranslationType::create($data);

        Logger::log($translationType);

        return ApiResponse::sendResponse(__('messages.translationType_create'),new TranslationTypeResource($translationType));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new translationType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.translationType_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $translationType = TranslationType::find($id);
        if(!$translationType){
            return ApiResponse::sendError(__('messages.translationType_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_translationType'),new TranslationTypeResource($translationType));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TranslationTypeStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $translationType = TranslationType::findOrFail($id);
            $translationType->update($data);
            
        return ApiResponse::sendResponse(__('messages.translationType_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating translationType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.translationType_update_error'));
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
        $translationType = TranslationType::find($id);
         if(!$translationType){
            return ApiResponse::sendError(__('messages.translationType_not_found'));
         }
         $translationType->delete();
         return ApiResponse::sendResponse(__('messages.translationType_delete'));
        }
}
