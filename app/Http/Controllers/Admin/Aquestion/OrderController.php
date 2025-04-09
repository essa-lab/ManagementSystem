<?php

namespace App\Http\Controllers\Admin\Aquestion;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Aqquestion\ChangeOrderStatusRequest;
use App\Http\Requests\Aqquestion\OrderRequest;
use App\Http\Requests\Aqquestion\StoreOrderItemRequest;
use App\Http\Requests\Aqquestion\StoreOrderRequest;
use App\Http\Resources\Aquisition\OrderItemResource;
use App\Http\Resources\Aquisition\OrderResource;
use App\Http\Resources\PaginatingResource;
use App\Models\Aquisition\Order;
use App\Models\Aquisition\OrderLog;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OrderRequest $request)
    {
        //
        $request->validated();
        $order = Order::query();
        $user = Auth::user();
        if(!Authorize::isSuperAdmin($user)){
            $order->where('library_id',$user->library_id);
        }
        if($request->has('po_number')){
            $order->where('po_number',$request->get('po_number'));
        }

        if($request->has('status')){
            $order->where('status',$request->get('status'));
        }

        $order->with(['createdBy','library','approvedBy']);
        $order->orderBy($request->get('sortBy','id'),$request->get('sortOrder','asc'));
        $order = $order->paginate($request->get('limit',10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($order,OrderResource::class));    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        
        $data = $request->validated();
        $user = Auth::user();
        try{
            if(!Authorize::isSuperAdmin($user) && Auth::user()->library_id != $request->get('library_id')){
                return ApiResponse::sendError(__('messages.not_authorized'));
            }
            $data['created_by'] = $user->id;
            $data['status'] = 'pending';
            $data['po_number'] = uniqid("PO_");

        $order =Order::create($data);

            OrderLog::create([
                'purchase_order_id'=>$order->id,
        'changed_by'=>$user->id,
        
        'status'=>'pending'
            ]);

        return ApiResponse::sendResponse(__('messages.order_create'),new OrderResource($order));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new order : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.order_create_error'));

        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //

        $order = Order::with(['createdBy','library','approvedBy','logs','orderItems'])->find($id);
        if(!$order){
            return ApiResponse::sendError(__('messages.order_not_found'));
        }

        return ApiResponse::sendResponse(__('messages.get_order'),new OrderResource($order));
    }

    public function changeStatus(ChangeOrderStatusRequest $request , $id)
    {
        //
        $request->validated();

        $order = Order::find($id);
        if(!$order){
            return ApiResponse::sendError(__('messages.order_not_found'));
        }
        $user =Auth::user();
        $order->status = $request->get('status');
        if($request->get('status')=='approved'){
            $order->approved_by = $user->id;
        }

        $order->save();

        OrderLog::create([
            'purchase_order_id'=>$order->id,
    'changed_by'=>$user->id,
    
    'status'=>$request->get('status')
        ]);



        return ApiResponse::sendResponse(__('messages.get_order'),new OrderResource($order));
    }

    public function storeItem(StoreOrderItemRequest $request,$id)
    {
        
        $data = $request->validated();
        try{

        $order = Order::find($id);
        if(!$order){
            return ApiResponse::sendError(__('messages.order_not_found'));
        }

        $totalAddedCost = 0;

        foreach ($data['items'] as $itemData) {
            $order->orderItems()->create([
                'title' => $itemData['title'],
                'type' => $itemData['type'],

                'author' => $itemData['author'] ?? null,
                'isbn' => $itemData['isbn'] ?? null,
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
            ]);

            $totalAddedCost += $itemData['quantity'] * $itemData['price'];
            
        }

        $order->increment('total_order_cost', $totalAddedCost);


        return ApiResponse::sendResponse(__('messages.order_item_created'));
        
        }catch(\Exception $e){
            Logger::log('Error Creating new order : '.$e->getMessage());

            return ApiResponse::sendError(__('messages.order_create_error'));

        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(SubjectStoreRequest $request, string $id)
    // {
    //     if(!Authorize::isSuperAdmin(Auth::user())){
    //         return ApiResponse::sendError(__('messages.not_authorized'));
    //     }
    //     $data = $request->validated();
    //     try{

    //         $subject =Subject::findOrFail($id);
    //         $subject->update($data);
    //     return ApiResponse::sendResponse(__('messages.subject_update'));
        
    //     }catch(\Exception $e){
    //         Logger::log('Error updating subject : '.$e->getMessage());

    //         return ApiResponse::sendError(__('messages.subject_update_error'));
    //     }
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     if(!Authorize::isSuperAdmin(Auth::user())){
    //         return ApiResponse::sendError(__('messages.not_authorized'));
    //     }
    //     $subject = Subject::find($id);
    //      if(!$subject){
    //         return ApiResponse::sendError(__('messages.subject_not_found'));
    //      }
    //      $subject->delete();
    //      return ApiResponse::sendResponse(__('messages.subject_delete'));
    //     }
}
