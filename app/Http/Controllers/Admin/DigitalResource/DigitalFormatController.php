<?php

namespace App\Http\Controllers\Admin\DigitalResource;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalResource\DigitalFormat\DigitalFormatRequest;
use App\Http\Requests\DigitalResource\DigitalFormat\DigitalFormatStoreRequest;
use App\Http\Resources\DigitalResource\DigitalFormatResource;
use App\Http\Resources\PaginatingResource;
use App\Models\DigitalResource\DigitalFormat;
use Illuminate\Support\Facades\Auth;


class DigitalFormatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DigitalFormatRequest $request)
    {
        //
        $request->validated();
        $digitalFormat = DigitalFormat::query();

        if($request->has('search')){
            $searchTerm = $request->get('search');
            $digitalFormat->where(function($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('title_ku', 'like', '%'.$searchTerm.'%');
            });
        }


        $digitalFormat->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $digitalFormat = $digitalFormat->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($digitalFormat,DigitalFormatResource::class));       }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DigitalFormatStoreRequest $request)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

        $digitalFormat =DigitalFormat::create($data);

        Logger::log($digitalFormat);

        return ApiResponse::sendResponse(__('messages.digitalFormat_create'),new DigitalFormatResource($digitalFormat));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new digitalFormat : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.digitalFormat_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $digitalFormat = DigitalFormat::find($id);
        if(!$digitalFormat){
            return ApiResponse::sendError(__('messages.digitalFormat_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_digitalFormat'),new DigitalFormatResource($digitalFormat));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DigitalFormatStoreRequest $request, string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $data = $request->validated();
        try{

            $digitalFormat =DigitalFormat::findOrFail($id);
            $digitalFormat->update($data);
        return ApiResponse::sendResponse(__('messages.digitalFormat_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating digitalFormat : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.digitalFormat_update_error'));
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
        $digitalFormat = DigitalFormat::find($id);
         if(!$digitalFormat){
            return ApiResponse::sendError(__('messages.digitalFormat_not_found'));
         }
         $digitalFormat->delete();
         return ApiResponse::sendResponse(__('messages.digitalFormat_delete'));
        }
}
