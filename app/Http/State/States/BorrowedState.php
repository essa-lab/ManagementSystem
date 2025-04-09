<?php
namespace App\Http\State\States;

use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;

class BorrowedState implements BorrowingState {
    public function handle(BorrowingRequest $request) {
        echo "The book has been borrowed.";
    }

    public function getState(): string {
        return 'borrowed';
    }
}
