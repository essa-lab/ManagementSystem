<?php
namespace App\Http\State;

use App\Http\State\States\BorrowedState;
use App\Http\State\States\OverdueState;
use App\Http\State\States\PendingState;
use App\Http\State\States\RejectedState;
use App\Http\State\States\ReturnedState;
use Exception;

class BorrowingStateFactory {
    public static function create(string $status): BorrowingState {
        return match ($status) {
            'pending' => new PendingState(),
            'borrowed' => new BorrowedState(),
            'overdue' => new OverdueState(),
            'returned' => new ReturnedState(),
            'rejected' => new RejectedState(),
            default => throw new Exception("Invalid state: $status"),
        };
    }
}
