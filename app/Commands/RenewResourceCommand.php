<?php
namespace App\Commands;

use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use App\Models\Resource\ResourceSetting;
use Exception;

class RenewResourceCommand
{
    private $circulation;

    public function __construct(Circulation $circulation)
    {
        $this->circulation = $circulation;
    }

    /**
     * Execute the renewal process.
     *
     * @throws Exception
     */
    public function execute()
    {
        $this->authorize();
        $this->validateRenewal();
        $this->logRenewalRequest();
    }

    private function authorize()
    {
        if (auth('patron')->user()->id !== $this->circulation->patron_id) {
            throw new Exception(__('messages.circulation_not_for_user'));
        }
    }

    private function validateRenewal()
    {
        if ($this->circulation->status === 'overdue') {
            throw new Exception(__('messages.patron_has_penalty'));
        }

        $setting = ResourceSetting::where('resource_id', $this->circulation->resourceCopy->resource_id)->first();

        if (!$setting) {
            throw new Exception(__('messages.no_settings'));
        }

        if ($setting->max_allowed_day == 0) {
            throw new Exception(__('messages.circulation_cant_be_renew'));
        }

        if ($this->circulation->circulation_count >= $setting->renewal_cycle) {
            throw new Exception(__('messages.circulation_not_anymore'));
        }
    }

    private function logRenewalRequest()
    {
        CirculationLog::create([
            'circulation_id' => $this->circulation->id,
            'action_date' => now(),
            'status' => 'request_renew'
        ]);
    }
}
