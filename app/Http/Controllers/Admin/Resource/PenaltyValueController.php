<?php

namespace App\Http\Controllers\Admin\Resource;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\PenaltyValue\PenaltyValueRequest;
use App\Http\Requests\Resource\PenaltyValue\PenaltyValueStoreRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\PenaltyValueResource;
use App\Models\Resource\PenaltyValue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class PenaltyValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(penaltyValueRequest $request)
    {
        //
        $request->validated();
        $penaltyValue = PenaltyValue::query();

        $penaltyValue->with('createdBy');

        $penaltyValue->orderBy($request->get('sortBy','id'),$request->get('sortOrder','desc'));
        $penaltyValue = $penaltyValue->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($penaltyValue,PenaltyValueResource::class));       }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PenaltyValueStoreRequest $request)
    {
        
        $data = $request->validated();
        try{

            $data['created_by']=Auth::user()->id;
            $data['created_at']=Carbon::now();
            $penaltyValue = PenaltyValue::first();

        $penaltyValue->update($data);


        return ApiResponse::sendResponse(__('messages.penaltyValue_create'),$penaltyValue);
        
        }catch(\Exception $e){
            Logger::log('Error Creating new penaltyValue : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.penaltyValue_create_error'));

        }
    }



}
