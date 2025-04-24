<?php

namespace App\Http\Controllers\Admin\Circulation;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\ResourceCopy\ChangeCirculationRenewRequest;
use App\Http\Requests\Resource\ResourceCopy\ChangeCirculationRequest;
use App\Http\Requests\Resource\ResourceCopy\CheckInRequest;
use App\Http\Requests\Resource\ResourceCopy\CheckOutRequest;
use App\Http\Requests\Resource\ResourceCopy\CirculationRequest;
use App\Http\Requests\Resource\ResourceCopy\RenewBook;
use App\Http\Requests\Resource\ResourceCopy\RenewRequest;
use App\Http\Requests\Resource\ResourceCopy\RequestBook;
use App\Http\Requests\Resource\ResourceCopy\WaivePenaltyRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\PatronCirculationResource;
use App\Http\Resources\Resource\CheckPenaltyResource;
use App\Http\Resources\Resource\CirculationResource;
use App\Http\Resources\Resource\RenewRequestResource;
use App\Http\Resources\Resource\TrackInventoryResource;
use App\Http\State\BorrowingRequest;
use App\Models\Library;
use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use App\Models\Resource\PenaltyWaiver;
use App\Queries\GetPatronCirculationsQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CirculationController extends Controller
{

    public function requestResource(RequestBook $request)
    {
        $request->validated();
        try {
            (new BorrowingRequest([
                'resource_id' => $request->get('resource_id'),
                'status' => 'pending'
            ]))->process();
            return response()->json([
                'message' => __('messages.requested_successfully'),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function renewResource(RenewBook $request)
    {
        $request->validated();
        try {
            (new BorrowingRequest([
                'circulation_id' => $request->get('circulation_id'),
                'status' => 'pending'
            ]))->process();
            return response()->json(['message' => __('messages.renew_request')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function circulationPatronLog(Request $request)
    {
        $patronId = auth('patron')->user()->id;

        try {
            $circulations = (new GetPatronCirculationsQuery($patronId))->execute(10);

            return response()->json([
                'data' => PatronCirculationResource::collection($circulations->items()),
                'meta' => [
                    'total' => $circulations->total(),
                    'limit' => $circulations->perPage(),
                    'page' => $circulations->currentPage(),
                    'previous' => $circulations->currentPage() > 1 ? $circulations->currentPage() - 1 : null,
                    'next' => $circulations->hasMorePages() ? $circulations->currentPage() + 1 : null,
                    'last' => $circulations->lastPage(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function adminCheckIn(CheckInRequest $request)
    {
        $request->validated();
        try {
            (new BorrowingRequest([
                'barcode' => $request->get('barcode'),
                'status' => 'returned'
            ]))->process();
            return response()->json([
                'message' => __('messages.checkid_in'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function adminCheckOut(CheckOutRequest $request)
    {
        $request->validated();
        try {
            (new BorrowingRequest([
                'status'=> 'borrowed',
                'patron_id' => $request->get('patron_id'),
                'barcode' => $request->get('barcode'),
            ]))->process();
            return response()->json([
                'message' => __('messages.checked_out'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    
    public function changeCirculationStatus(ChangeCirculationRequest $request)
    {
        $request->validated();
        try {
            (new BorrowingRequest([
                'status'=> $request->get('status'),
                'circulation_id' => $request->get('circulation_id'),
            ]))->process();
            return response()->json([
                'message' => __('messages.resource_status_chenged'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function changeCirculationRenewStatus(ChangeCirculationRenewRequest $request)
    {
        $request->validated();
        try {
            (new BorrowingRequest([
                'status'=> $request->get('status'),
                'circulation_id' => $request->get('circulation_id'),
                'duration'=> $request->get('duration'),
            ]))->process();
            return response()->json([
                'message' => __('messages.resource_status_chenged'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function payPenalty(string $id)
    {
        try {


            $circulation = Circulation::with(['penalties', 'patron'])->find($id);
            if (!$circulation) {
                return response()->json([
                    'error' => __('messages.circulation_not_found'),
                ], 400);
            }
            $user = Auth::user();
            if ($circulation->resourceCopy->resource->library_id != $user->library_id) {
                return ApiResponse::sendError(__('messages.another_library'));
            }
            if (empty($circulation->penalties)) {
                return response()->json([
                    'error' => __('messages.no_penalty'),
                ], 400);
            }

            $penalty = $circulation->penalties->last();
            if ($penalty->is_paid) {
                return response()->json([
                    'error' => __('messages.already_paid'),
                ], 400);
            }
            $borrowDate = $circulation->borrow_date;
            $dueDate = $circulation->due_date;


            $receiptData = [
                'receipt_number' => uniqid("RCP_"),
                'date' => now()->format('y-m-d'),
                'customer' => $circulation->patron->name,
                // 'payment_method' => 'Library Account',
                'borrow_date' => $borrowDate?->format('y-m-d'),
                'due_date' => $dueDate?->format('y-m-d'),
                'overdue_days' => $penalty->days_overdue,
                'name' => $circulation->resourceCopy->resource->{'title_' . app()->getLocale()},
                'total' => $penalty->total_penalty_amount,
            ];

            $pdf = Pdf::loadView('receipts.template', compact('receiptData'));
            $penalty->is_paid = 1;
            $penalty->updated_by = Auth::user()->id;

            $penalty->save();
            return response()->streamDownload(
                fn() => print($pdf->output()),
                'receipt.pdf',
                ['Content-Type' => 'application/pdf']
            );
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function waivePenalty(WaivePenaltyRequest $request)
    {

        $data = $request->validated();
        try {
            $circulation = Circulation::with(['penalties', 'patron'])->find($data['circulation_id']);
            if (!$circulation) {
                return response()->json([
                    'error' => __('messages.circulation_not_found'),
                ], 400);
            }
            $user = Auth::user();
            if ($circulation->resourceCopy->resource->library_id != $user->library_id) {
                return ApiResponse::sendError(__('messages.another_library'));
            }
            if (empty($circulation->penalties)) {
                return response()->json([
                    'error' => __('messages.no_penalty'),
                ], 400);
            }

            $penalty = $circulation->penalties->last();
            if ($penalty->is_paid) {
                return response()->json([
                    'error' => __('messages.already_paid'),
                ], 400);
            }

            PenaltyWaiver::create([
                'waived_by' => Auth::user()->id,
                'penlaty_id' => $penalty->id,
                'reason' => $data['reason']
            ]);

            $penalty->is_paid = 1;
            $penalty->updated_by = Auth::user()->id;

            $penalty->save();
            return response()->json([
                'message' => __('messages.penalty_waived'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function index(CirculationRequest $request)
    {
        //
        $request->validated();
        $circulation = Circulation::query();
        $circulation->with(['resourceCopy', 'resourceCopy.resource', 'patron']);
        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user)) {
            $circulation->whereHas('resourceCopy.resource', function ($query) use ($user) {
                $query->where('library_id', $user->library_id);
            });
        }
        if ($request->has('resource_id')) {
            $resourceId = $request->get('resource_id');
            $circulation->whereHas('resourceCopy.resource', function ($query) use ($resourceId) {
                $query->where('id', $resourceId);
            });
        }
        if ($request->has('status')) {
            $circulation->where('status', $request->get('status'));
            if ($request->get('status') == 'overdue') {
                $circulation->with('latestPenalty');
            }
        }
        if ($request->has('patron_id')) {
            $circulation->where('patron_id', $request->get('patron_id'));
        }
        $circulation->orderBy($request->get('sortBy', 'id'), $request->get('sortOrder', 'asc'));
        $circulation = $circulation->paginate($request->get('limit', 10));
        return ApiResponse::sendPaginatedResponse(new PaginatingResource($circulation, CirculationResource::class));
    }
    public function logIndex(string $id)
    {
        $circulation = CirculationLog::with('actionBy')->where('circulation_id', $id)->orderBy('id', 'desc')->get();
        return ApiResponse::sendResponse($circulation);
    }
    public function getResourceCopyCounts($libraryId = null)
    {
        $query = Library::query()
            ->withCount([
                'resources as borrowed_count' => function ($query) {
                    $query->join('resource_copies', 'resources.id', '=', 'resource_copies.resource_id')
                        ->where('resource_copies.status', 'borrowed');
                },
                'resources as available_count' => function ($query) {
                    $query->join('resource_copies', 'resources.id', '=', 'resource_copies.resource_id')
                        ->where('resource_copies.status', 'available');
                },
                'resources as reserved_count' => function ($query) {
                    $query->join('resource_copies', 'resources.id', '=', 'resource_copies.resource_id')
                        ->where('resource_copies.status', 'reserved');
                }
            ]);

        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user)) {
            $query->where('id', $user->library_id);
        }


        return ApiResponse::sendResponse('Track Inventory', TrackInventoryResource::collection($query->get()));
    }
    public function checkPenalty()
    {
        $patronId = auth('patron')->user()->id;
        try {
            $circulations = Circulation::with(['latestPenalty', 'resourceCopy.resource'])
                ->where('patron_id', $patronId)
                ->whereRelation('latestPenalty', 'is_paid', 0)
                ->get();


            return ApiResponse::sendResponse(__('mesages.liabilty'), CheckPenaltyResource::collection($circulations));
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function checkPenaltyForPatron(Request $req)
    {
        $patronId = $req->get('patron_id', null);
        if (!$patronId) {
            return response()->json([
                'error' => "patron_id required",
            ], 400);
        }
        try {
            $circulations = Circulation::with(['latestPenalty', 'resourceCopy.resource'])
                ->where('patron_id', $patronId)
                ->whereRelation('latestPenalty', 'is_paid', 0)
                ->get();


            return ApiResponse::sendResponse(__('messages.liabilty'), $circulations);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function generatePenaltyForPatron(Request $req)
    {
        $patronId = $req->get('patron_id', null);
        if (!$patronId) {
            return response()->json([
                'error' => "patron_id required",
            ], 400);
        }
        try {
            $circulations = Circulation::with(['latestPenalty', 'resourceCopy.resource', 'patron'])
                ->where('patron_id', $patronId)
                ->whereRelation('latestPenalty', 'is_paid', 0)
                ->get();

            $pdf = Pdf::loadView('reports.penalty', compact('circulations'));
            return response()->streamDownload(
                fn() => print($pdf->output()),
                'patron-penalty.pdf',
                ['Content-Type' => 'application/pdf']
            );
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function showOverdue($id)
    {

        $circulation = Circulation::with(['resourceCopy', 'resourceCopy.resource', 'patron'])->find($id);


        if (!$circulation) {
            return ApiResponse::sendError(__('messages.circulation_not_found'));
        }
        if ($circulation->status != 'overdue') {
            return ApiResponse::sendError(__('messages.not_overdue'));
        }

        return ApiResponse::sendResponse('overdue circulation', new CirculationResource($circulation));
    }
    public function getRenewalRequest(RenewRequest $request)
    {

        $data = $request->validated();

        $logs = CirculationLog::with('circulation', 'circulation.patron', 'circulation.resourceCopy.resource')->whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('circulation_logs')
                ->groupBy('circulation_id');
        })
            ->where('status', 'request_renew')
            ->when($request->has('patron_id'), function ($query) use ($data) {
                $query->whereHas('circulation', function ($query) use ($data) {
                    $query->where('patron_id', $data['patron_id']);
                });
            })
            ->paginate($data['limit'] ?? 10);

        return ApiResponse::sendPaginatedResponse(new PaginatingResource($logs, RenewRequestResource::class));
    }
}
