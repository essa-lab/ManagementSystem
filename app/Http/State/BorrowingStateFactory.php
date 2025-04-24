<?php
namespace App\Http\State;

use App\Http\State\States\ApprovedState;
use App\Http\State\States\BorrowedState;
use App\Http\State\States\OverdueState;
use App\Http\State\States\PendingState;
use App\Http\State\States\RejectedState;
use App\Http\State\States\RenewAcceptedState;
use App\Http\State\States\RenewRejectedState;
use App\Http\State\States\RenewReuest;
use App\Http\State\States\ReturnedState;
use Exception;

class BorrowingStateFactory {
    public static function create(string $status): BorrowingState {
        return match ($status) {
            'pending' => new PendingState(),
            'borrowed' => new BorrowedState(),
            'request_approved' => new ApprovedState(),
            'renew' => new RenewReuest(),
            'returned' => new ReturnedState(),
            'request_rejected' => new RejectedState(),
            'renew_accepted'=> new RenewAcceptedState(),
            'renew_rejected'=> new RenewRejectedState(),

            default => throw new Exception("Invalid state: $status"),
        };
    }
}
