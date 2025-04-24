<?php
namespace App\Http\State\States;

use App\Helper\Authorize;
use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use Exception;
use Illuminate\Support\Facades\Auth;

class RenewRejectedState implements BorrowingState {
    private Circulation $circulation;
    private $user;
    public function handle(BorrowingRequest $request) {
        $this->circulation = Circulation::find($request->getData()['circulation_id']);
        $this->user = Auth::user();
        $this->checkAuthority();
       
        $this->createLog();

   
    }
    public function getState(): string {
        return 'renew_rejected';
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
            'status' => 'renew_rejected',
            'action_by' => $this->user->id
        ]);
    }
}
     