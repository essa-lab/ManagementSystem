<?php
namespace App\Commands;

use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use App\Models\Resource\Resource;
use App\Models\Resource\ResourceCopy;
use App\Models\Resource\ResourceSetting;
use Exception;

class RequestResourceCommand
{
    private $resourceId;
    private $patronId;
    private $resource;
    private $availableCopy;

    public function __construct(int $resourceId, int $patronId)
    {
        $this->resourceId = $resourceId;
        $this->patronId = $patronId;
    }

    public function execute()
    {
        $this->validateResource();
        $this->checkAvailability();
        $this->updateResourceAvailability();
        $this->reserveCopy();
        $circulation = $this->createCirculation();
        $this->logRequest($circulation);

        return [
            'message' => __('messages.requested_successfully'),
            'copy_id' => $this->availableCopy->id
        ];
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
