<?php
namespace App\Queries;

use App\Models\Resource\Circulation;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPatronCirculationsQuery
{
    private $patronId;

    public function __construct(int $patronId)
    {
        $this->patronId = $patronId;
    }

    public function execute(int $perPage = 10): LengthAwarePaginator
    {
        return Circulation::with(['logs', 'penalties', 'resourceCopy.resource.library', 'resourceCopy.resource.reviews'])
            ->where('patron_id', $this->patronId)
            ->paginate($perPage)
            ->through(fn($circulation) => $this->transformCirculation($circulation));
    }

    private function transformCirculation($circulation): array
    {
        $dateBorrowed = $circulation->borrow_date;
        $dueDate = $circulation->due_date;
        $status = $circulation->status;

        $dateRenewed = $circulation->circulation_count > 0
            ? optional($circulation->logs->where('status', 'renew_accepted')->sortByDesc('action_date')->first())->action_date
            : null;

        $review = optional($circulation->resourceCopy->resource->reviews
            ->where('patron_id', $this->patronId)
            ->sortByDesc('action_date')
            ->first());

        $penalty = $circulation->penalties->last();
        $penaltyAmount = optional($penalty)->total_penalty_amount;
        $penaltyCleared = $penalty ? ($penalty->is_paid ? 'Paid' : 'Not Paid') : null;

        return [
            'id' => $circulation->id,
            'resource' => $circulation->resourceCopy->resource,
            'review' => $review,
            'date_borrowed' => $dateBorrowed,
            'date_renewed' => $dateRenewed,
            'due_date' => $dueDate,
            'status' => $status,
            'penalty_amount' => $penaltyAmount,
            'penalty_cleared' => $penaltyCleared
        ];
    }
}
