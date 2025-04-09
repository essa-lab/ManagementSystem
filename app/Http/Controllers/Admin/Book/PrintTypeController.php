<?php

namespace App\Http\Controllers\Admin\Book;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Books\PrintType\PrintTypeRequest;
use App\Http\Requests\Books\PrintType\PrintTypeStoreRequest;
use App\Http\Resources\Book\PrintTypeResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Book\PrintType;
use Illuminate\Support\Facades\Auth;


class PrintTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PrintTypeRequest $request)
    {
        //
        $request->validated();
        $printType = PrintType::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $printType->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }

        $printType->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $printType = $printType->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($printType,PrintTypeResource::class));    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrintTypeStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $printType = PrintType::create($data);

        Logger::log($printType);

        return ApiResponse::sendResponse(__('messages.printType_create'),new PrintTypeResource($printType));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new printType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.printType_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $printType = PrintType::find($id);
        if(!$printType){
            return ApiResponse::sendError(__('messages.printType_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_printType'),new PrintTypeResource($printType));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PrintTypeStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $printType = PrintType::findOrFail($id);
            $printType->update($data);
            
        return ApiResponse::sendResponse(__('messages.printType_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating printType : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.printType_update_error'));
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
        $printType = PrintType::find($id);
         if(!$printType){
            return ApiResponse::sendError(__('messages.printType_not_found'));
         }
         $printType->delete();
         return ApiResponse::sendResponse(__('messages.printType_delete'));
        }
}
