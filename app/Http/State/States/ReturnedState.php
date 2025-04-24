<?php
namespace App\Http\State\States;

use App\Helper\Authorize;
use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use App\Models\Resource\ResourceCopy;
use App\Models\Resource\ResourceSetting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class ReturnedState implements BorrowingState {
    private ResourceCopy $resourceCopy;
    private User $user;
    public function handle(BorrowingRequest $request) {
        $this->resourceCopy = ResourceCopy::with('resource.resourceSetting')->where('barcode', $request->getData()['barcode'])->first();
        $this->user = Auth::user();

        $this->checkAuthority();
        $this->checkAvailability();
        $circulation = Circulation::where('resource_copy_id', $this->resourceCopy->id)
                ->latest()
                ->first();
                
        $this->checkPenalty($circulation);
        $this->checkIn($circulation);
        $this->logReturn($circulation);           
    }

    public function getState(): string {
        return 'returned';
    }

    private function checkAuthority(){
        if (!Authorize::isSuperAdmin($this->user) && $this->resourceCopy->resource->library_id != $this->user->library_id) {
            throw new Exception(__('messages.unathorized'));
        }
    }
    private function checkAvailability(){
        if ($this->resourceCopy->status == 'available') {
            throw new Exception(__('messages.not_cheked_out'));
        }
    }
    private function checkPenalty(Circulation $circulation){
        if ($circulation->status == 'overdue' && !$circulation->penalties->last()->is_paid) {
            throw new Exception(
                __('messages.outstanding_payment')
            );
        }
    }
    private function checkIn(Circulation $circulation){
        $this->resourceCopy->status = 'available';
        $this->resourceCopy->save();
        $setting = ResourceSetting::where('resource_id', $this->resourceCopy->resource_id)->first();
        $setting->availability = 1;
        $setting->save();
        $circulation->update([
            'status' => 'returned',
            'return_date' => now()
        ]);
    }
    private function logReturn(Circulation $circulation){
        CirculationLog::create([
            'circulation_id' => $circulation->id,
            'action_date' => now(),
            'status' => 'returned',
            'action_by' => $this->user->id
        ]);
    }
}