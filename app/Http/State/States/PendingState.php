<?php
namespace App\Http\State\States;

use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use App\Models\Resource\Resource;
use App\Models\Resource\ResourceCopy;
use App\Models\Resource\ResourceSetting;
use Exception;

class PendingState implements BorrowingState {
    private int $resourceId;
    private int $patronId;
    private Resource $resource;
    private ResourceCopy $availableCopy;

    
    public function handle(BorrowingRequest $request) {

        $this->resourceId = $request->getData()['resource_id'];
        $this->patronId = auth('patron')->user()->id;

        $this->validateResource();
        $this->checkAvailability();
        $this->updateResourceAvailability();
        $this->reserveCopy();
        $circulation = $this->createCirculation();
        $this->logRequest($circulation);
    }

    public function getState(): string {
        return 'pending';
    }

    private function validateResource()
    {
        $this->resource = Resource::with('resourceSetting')->find($this->resourceId);

        if (!$this->resource || !$this->resource->resourceSetting?->availability) {
            throw new Exception(__('messages.resource_not_available_to_circulate'));
        }
    }

    private function checkAvailability()
    {
        $this->availableCopy = ResourceCopy::where('resource_id', $this->resourceId)
            ->where('status', 'available')
            ->first();

        if (!$this->availableCopy) {
            throw new Exception(__('messages.no_copies_left'));
        }
    }

    private function updateResourceAvailability()
    {
        $remainingCopies = ResourceCopy::where('resource_id', $this->resourceId)
            ->where('status', 'available')
            ->count();

        if ($remainingCopies == 1) {
            ResourceSetting::where('resource_id', $this->resourceId)->update([
                'availability' => 0
            ]);
        }
    }

    private function reserveCopy()
    {
        $this->availableCopy->update([
            'status' => 'reserved',
        ]);
    }

    private function createCirculation()
    {
        return Circulation::create([
            'resource_copy_id' => $this->availableCopy->id,
            'patron_id' => $this->patronId,
            'status' => 'pending'
        ]);
    }

    private function logRequest(Circulation $circulation)
    {
        CirculationLog::create([
            'circulation_id' => $circulation->id,
            'action_date' => now(),
            'status' => 'request_submitted'
        ]);
    }

    
}
