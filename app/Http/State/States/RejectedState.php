<?php
namespace App\Http\State\States;

use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
class RejectedState implements BorrowingState {
    public function handle(BorrowingRequest $request) {
        echo "The resource has been rejeceted.";
    }

    public function getState(): string {
        return 'rejected';
    }
}