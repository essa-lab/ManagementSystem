<?php
namespace App\Http\State\States;

use App\Helper\Authorize;
use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use App\Models\Resource\ResourceSetting;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class RenewAcceptedState implements BorrowingState {
    private Circulation $circulation;
    private $user;
    public function handle(BorrowingRequest $request) {
        $this->circulation = Circulation::find($request->getData()['circulation_id']);
        $this->user = Auth::user();
        $this->checkAuthority();

        if ($this->circulation->status == 'overdue') {
            throw new Exception(__('messages.patron_has_penalty'));
        }

            $setting = ResourceSetting::where('resource_id', $this->circulation->resourceCopy->resource->id)->first();

            if ($setting && $setting->allow_renewal && $setting->max_allowed_day > 0) {
                $this->circulation->due_date = Carbon::parse($this->circulation->due_date)->addDays((int)$request->getData()['duration']);
                $this->circulation->circulation_count += 1;

                $this->circulation->save();
            }else{
                throw new Exception(__('messages.circulation_cant_be_renew'));
            }
            
        
        $this->createLog();

   
    }
    public function getState(): string {
        return 'renew_accepted';
    }

    private function checkAuthority()
    {
        if (!Authorize::isSuperAdmin($this->user) && $this->circulation->resourceCopy->resource->library_id != $this->user->library_id) {
            throw new Exception(__('messages.unathorized'));
        }
    }
    private function createLog()
    {
        CirculationLog::create([
            'circulation_id' => $this->circulation->id,
            'action_date' => now(),
            'status' => 'renew_accepted',
            'action_by' => $this->user->id
        ]);
    }
}
     