<?php
namespace App\Http\State;
interface BorrowingState{
    public function handle(BorrowingRequest $request);
    public function getState(): string;

}