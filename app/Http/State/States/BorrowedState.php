<?php

namespace App\Http\State\States;

use App\Helper\Authorize;
use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use App\Models\Resource\ResourceCopy;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class BorrowedState implements BorrowingState
{

    private $resourceCopy;
    private $user;
    public function handle(BorrowingRequest $request)
    {

        $this->resourceCopy = ResourceCopy::with('resource')->where('barcode', $request->getData()['barcode'])->first();
        $this->user = Auth::user();

        $this->checkAuthority();
        $this->checkEligibility($request->getData()['patron_id']);

        $circulation = Circulation::where('resource_copy_id', $this->resourceCopy->id)
            ->where('patron_id', $request->getData()['patron_id'])
            ->where('status', 'approved')
            ->first();

        if ($circulation) {
            $circulation->update([
                'status' => 'borrowed',
                'due_date' => Carbon::now()->addDays($this->resourceCopy->resource->resourceSetting->max_allowed_day),
                'borrow_date' => now(),
                'circulation_count' => 1,
            ]);
        } else {
            //in case of borrowing without reservation
            $circulation = Circulation::create([
                'resource_copy_id' => $this->resourceCopy->id,
                'patron_id' => $request->getData()['patron_id'],
                'status' => 'borrowed',
                'due_date' => Carbon::now()->addDays($this->resourceCopy->resource->resourceSetting->max_allowed_day),
                'borrow_date' => now(),
                'circulation_count' => 1,
            ]);
        }


        $this->createLog($circulation);
    }

    public function getState(): string
    {
        return 'borrowed';
    }

    private function checkAuthority()
    {
        if (
            !Authorize::isSuperAdmin($this->user) &&
            $this->resourceCopy->resource->library_id != $this->user->library_id
        ) {
            throw new Exception(__('messages.unathorized'));
        }
    }
    private function createLog(Circulation $circulation)
    {
        CirculationLog::create([
            'circulation_id' => $circulation->id,
            'action_date' => now(),
            'status' => 'borrowed',
            'action_by' => $this->user->id
        ]);
    }
    private function checkEligibility(int $patronId): void
    {
        $this->ensureNotLocked();
        $this->ensureAvailableOrReserved();
        $this->ensureReservationMatches($patronId);

        $this->markAsBorrowed();
    }

    private function ensureNotLocked(): void
    {
        if ($this->resourceCopy->resource->resourceSetting->locked) {
            throw new Exception(__('messages.resource_no_borrow'));
        }
    }

    private function ensureAvailableOrReserved(): void
    {
        if (!in_array($this->resourceCopy->status, ['available', 'reserved'])) {
            throw new Exception(__('messages.resource_not_available_to_circulate'));
        }
    }

    private function ensureReservationMatches(int $patronId): void
    {
        if ($this->resourceCopy->status === 'reserved') {
            $circulation = Circulation::where('resource_copy_id', $this->resourceCopy->id)->latest()->first();

            if (!$circulation || $patronId != $circulation->patron_id) {
                throw new Exception(__('messages.resource_for_another_patron'));
            }
        }
    }

    private function markAsBorrowed(): void
    {
        $this->resourceCopy->update(['status' => 'borrowed']);
    }
}
