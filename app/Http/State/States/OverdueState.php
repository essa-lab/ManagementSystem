<?php
namespace App\Http\State\States;

use App\Http\State\BorrowingRequest;
use App\Http\State\BorrowingState;
class OverdueState implements BorrowingState {
    public function handle(BorrowingRequest $request) {
        echo "The book has been overdue.";
    }

    public function getState(): string {
        return 'overdue';
    }
}
