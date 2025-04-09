<?php
namespace App\Http\State\States;

use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
class PendingState implements BorrowingState {
    public function handle(BorrowingRequest $request) {
        echo "The book has been pending.";
    }

    public function getState(): string {
        return 'pending';
    }
}
