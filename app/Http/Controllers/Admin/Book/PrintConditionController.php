<?php

namespace App\Http\Controllers\Admin\Book;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Books\PrintCondition\PrintConditionRequest;
use App\Http\Requests\Books\PrintCondition\PrintConditionStoreRequest;
use App\Http\Resources\Book\PrintConditionResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Book\PrintCondition;
use Illuminate\Support\Facades\Auth;


class PrintConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PrintConditionRequest $request)
    {
        //
        $request->validated();
        $printCondition = PrintCondition::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $printCondition->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $printCondition->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $printCondition = $printCondition->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($printCondition,PrintConditionResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrintConditionStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $printCondition = printCondition::create($data);

        Logger::log($printCondition);

        return ApiResponse::sendResponse(__('messages.printCondition_create'),new PrintConditionResource($printCondition));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new printCondition : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.printCondition_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $printCondition = PrintCondition::find($id);
        if(!$printCondition){
            return ApiResponse::sendError(__('messages.printCondition_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_printCondition'),new PrintConditionResource($printCondition));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PrintConditionStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $printCondition = PrintCondition::findOrFail($id);
            $printCondition->update($data);
            
        return ApiResponse::sendResponse(__('messages.printCondition_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating printCondition : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.printCondition_update_error'));
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
        $printCondition = PrintCondition::find($id);
         if(!$printCondition){
            return ApiResponse::sendError(__('messages.printCondition_not_found'));
         }
         $printCondition->delete();
         return ApiResponse::sendResponse(__('messages.printCondition_delete'));
        }
}
