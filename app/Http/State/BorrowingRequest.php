<?php
namespace App\Http\State;


class BorrowingRequest {

    private array $data;
    private BorrowingState $state;

    public function __construct(array $data) {
        $this->data = $data;
        $this->state = BorrowingStateFactory::create($data['status']);
    }

    public function getState(): BorrowingState {
        return $this->state;
    }

    public function getData(): array {
        return $this->data;
    }

    public function setState(BorrowingState $state) {
        $this->state = $state;  
    }

    public function process() {
        $this->state->handle($this);
    }
}
