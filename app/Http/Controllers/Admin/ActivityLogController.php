<?php
namespace App\Http\Controllers\Admin;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityLogRequest;

use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\PaginatingResource;
use App\Models\ActivityLog;

use Illuminate\Support\Facades\Auth;


class ActivityLogController extends Controller
{
    

    public function index(ActivityLogRequest $request){

        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $request->validated();
        $activityLog = ActivityLog::query();
        

        if($request->has('user_id')){
            $activityLog->where('user_id',$request->get('user_id'));
        }

        $activityLog->with('user');
        $activityLog->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $activityLog = $activityLog->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($activityLog,ActivityLogResource::class));

    }

   
}
