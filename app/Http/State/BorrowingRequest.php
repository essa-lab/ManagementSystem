<?php
namespace App\Http\State;

use Carbon\Carbon;

class BorrowingRequest {

    private Circulation $circulation;
    private BorrowingState $state;

    public function __construct(array $data) {
        $this->circulation = $data['circulation'];
        $this->state = BorrowingStateFactory::create($data['status']);
    }

    public function getState(): BorrowingState {
        return $this->state;
    }

    public function setState(BorrowingState $state) {
        if ($this->canTransitionTo($state)) {
            $this->state = $state;
        } else {
            throw new \Exception("Invalid state transition from '{$this->circulation->status}' to '{$state->getState()}'.");
        }    
    }

    public function process() {
        $this->state->handle($this);
    }

    private function canTransitionTo(BorrowingState $newState): bool {
        $currentStatus = $this->circulation->status;
        $newStatus = $newState->getState();
        $bookStatus = $this->circulation->book->status;

        if ($newStatus === 'pending' && $bookStatus !== 'available') {
            return false;
        }

        $allowedTransitions = [
            'pending' => ['borrowed', 'rejected'],
            'borrowed' => ['returned', 'overdue'],
            'rejected' => [], // Cannot transition from rejected
            'returned' => [], // Cannot transition from returned
            'overdue' => ['returned'], // Cannot go back to pending, borrowed, or rejected
        ];

        return in_array($newStatus, $allowedTransitions[$currentStatus] ?? [], true);
    }

}
