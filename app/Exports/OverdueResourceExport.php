<?php

namespace App\Exports;

use App\Helper\Authorize;
use App\Models\Resource\Circulation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OverdueResourceExport implements FromCollection , WithHeadings, WithMapping ,WithStyles
{

    protected $from;
    protected $to;


    public function __construct($from, $to)
    {
        

        $this->from = $from;
        $this->to = $to;

    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $user = Auth::user();
        return Circulation::where('status', 'overdue')->whereBetween('due_date', [$this->from, $this->to])

                ->with(['penalties', 'resourceCopy.resource' => function($query) use ($user) {
                    if (!Authorize::isSuperAdmin($user)) {
                        $query->where('resources.library_id', $user->library_id);
                    }
                }, 'patron'])
                ->get();
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' =>12]], 
        ];
    }
    public function headings(): array
    {
        return [
            
            __('messages.title'),
            __('messages.type'),
            __('messages.patron_report'),
            __('messages.patron_id'),
            __('messages.patron_internal'),

            __('messages.borrow_date'),
            __('messages.due_date'),
            __('messages.overdue_days'),
            __('messages.penalty'),

        ];
    }
    public function map($circulation): array
    {
        $penalty = $circulation->penalties->last();
        $overdueDays = $penalty->days_overdue;
        $amountPenalty = $penalty->total_penalty_amount;

        if($circulation->resourceCopy->resource){
            return [
                $circulation->resourceCopy->resource->{'title_' . app()->getLocale()},
                class_basename($circulation->resourceCopy->resource->resourceable_type),
                $circulation->patron->name,
                $circulation->patron->id,
                $circulation->patron->internal_identifier,
                $circulation->borrow_date,
                $circulation->due_date,
                $overdueDays,
                $amountPenalty
            ];
        }
        return [];
        
    }
}
