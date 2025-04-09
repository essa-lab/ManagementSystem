<?php
namespace App\Http\State\States;

use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
class ReturnedState implements BorrowingState {
    public function handle(BorrowingRequest $request) {
        echo "The resource has been returned.";
    }

    public function getState(): string {
        return 'returned';
    }
}