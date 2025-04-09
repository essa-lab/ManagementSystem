<?php

namespace App\Http\Controllers\Admin\Resource;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\Library\LibraryRequest;
use App\Http\Requests\Resource\Library\LibraryStoreRequest;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Library;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LibraryRequest $request)
    {
        $request->validated();
        $library = Library::query();
        $user = Auth::user();
        try{
            Authorize::hasPermission($user,'LIBRARY');

        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.anauthorized'));

        }
        if(!Authorize::isSuperAdmin($user)){
            $library->where('id',$user->library_id);
        }
        if($request->has('loadRelation')){
            $relationsArray = array_map('trim', explode(',', $request->get('loadRelation')));
            $library->with($relationsArray);
        }
        if($request->has('search')){
            $searchTerm = $request->get('search');
            $library->where(function($query) use ($searchTerm) {
                $query->where('name_ar', 'like', '%'.$searchTerm.'%')
                      ->orWhere('name_en', 'like', '%'.$searchTerm.'%')
                      ->orWhere('name_ku', 'like', '%'.$searchTerm.'%');
            });
        }
        $library->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $library = $library->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($library,LibraryResource::class));       
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(LibraryStoreRequest $request)
    {
        $data = $request->validated();
        try{

        $user = Auth::user();
        
        if(!Authorize::isSuperAdmin($user)){
            return ApiResponse::sendError(__('messages.anauthorized'));
        }

        $library = Library::create($data);

        Logger::log($library);

        return ApiResponse::sendResponse(__('messages.library_create'),new LibraryResource($library));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new library : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.library_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        try{
            $user= Auth::user();
            Authorize::hasPermission($user,'LIBRARY',$id);

        }catch(HttpException $e){
            return ApiResponse::sendError(__('messages.anauthorized'));

        }
        $library = Library::find($id);
        if(!$library){
            return ApiResponse::sendError(__('messages.library_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_library'),new LibraryResource($library));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LibraryStoreRequest $request, string $id)
    {
        $data = $request->validated();
        try{

            if(!Authorize::isSuperAdmin(Auth::user())){
                return ApiResponse::sendError(__('messages.anauthorized'));
            }

            $library = Library::findOrFail($id);
            $library->update($data);
        return ApiResponse::sendResponse(__('messages.library_update'));
        
        }catch(\Exception $e){
            Logger::log('Error updating library : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.library_update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(!Authorize::isSuperAdmin(Auth::user())){
            return ApiResponse::sendError(__('messages.anauthorized'));
        }
        $library = Library::find($id);
         if(!$library){
            return ApiResponse::sendError(__('messages.library_not_found'));
         }
         $library->delete();
         return ApiResponse::sendResponse(__('messages.library_delete'));
        }
}
