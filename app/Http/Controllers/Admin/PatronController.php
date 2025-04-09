<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Patron\PatronRequest;
use App\Http\Requests\Patron\PatronStoreRequest;
use App\Http\Requests\Patron\PatronUpdateRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\PatronResource;
use App\Models\Patron;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PatronController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PatronRequest $request)
    {
        //
        $request->validated();
        $patron = Patron::query();
        try{
            Authorize::hasPermission(Auth::user(),'PATRONS');
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'));

        }
        if($request->has('search')){
            $searchTerm = $request->get('search');
            $patron->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%'.$searchTerm.'%')
                      ->orWhere('internal_identifier', 'like', '%'.$searchTerm.'%');

            });       
         }
        if($request->has('status')){
            $patron->where('status',$request->get('status'));
        }
        $patron->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $patron = $patron->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($patron,PatronResource::class));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatronStoreRequest $request)
    {
        $data = $request->validated();
        try{
            Authorize::hasPermission(Auth::user(),'PATRONS');
            $data['password'] = Hash::make($data['password']);
            $data['remember_token'] = Str::random(16);
            $data['verified']= 1;
            $data['verified_at']=Carbon::now();
            $patron = Patron::create($data);
            return ApiResponse::sendResponse(__('messages.patron_create'),new PatronResource($patron));
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'));

        }catch(\Exception $e){
            Logger::log('Error Creating new patron : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.patron_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try{
            Authorize::hasPermission(Auth::user(),'PATRONS');
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'));

        }        
        $patron = Patron::find($id);
        if(!$patron){
            return ApiResponse::sendError(__('messages.patron_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_patron'),new PatronResource($patron));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatronUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        try{
            Authorize::hasPermission(Auth::user(),'PATRONS');

            if(isset($data['password'])){
                $data['password'] = Hash::make($data['password']);
            }

        $patron = Patron::findOrFail($id);
        if(isset($data['verified'])){
            $data['verified_at'] = Carbon::now();
        }
        $patron->update($data);
        return ApiResponse::sendResponse(__('messages.patron_update'));
        
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'));

        }catch(\Exception $e){
            Logger::log('Error updating patron : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.patron_update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            Authorize::hasPermission(Auth::user(),'PATRONS');
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'));
        } 
        $patron = Patron::find($id);
         if(!$patron){
            return ApiResponse::sendError(__('messages.patron_not_found'));
         }
         $patron->delete();
         return ApiResponse::sendResponse(__('messages.patron_delete'));
    }

    public function suspend(string $id){
        try{
            Authorize::hasPermission(Auth::user(),'PATRONS');
        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.not_authorized'));

        } 
        $patron = Patron::find($id);
        if(!$patron){
           return ApiResponse::sendError(__('messages.patron_not_found'));
        }
        $patron->suspend();
        return ApiResponse::sendResponse(__('messages.patron_update'));
    }
}
