<?php

namespace App\Exports;

use App\Helper\Authorize;
use App\Models\Resource\Circulation;
use App\Models\Resource\ResourceCopy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DamagedResourceExport implements FromCollection , WithHeadings, WithMapping ,WithStyles
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
        return ResourceCopy::where('status', 'damaged')->whereBetween('updated_at', [$this->from, $this->to])
        ->with(['resource' => function($query) use ($user) {
         if (!Authorize::isSuperAdmin($user)) {
             $query->where('library_id', $user->library_id); 
         }
     }])
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
            __('messages.locked'),
            __('messages.shelf_number'),
            __('messages.storage_location'),
            __('messages.barcode'),
        ];
    }

    public function map($circulation): array
    {
        if($circulation->resource){

        return [
            $circulation->resource->{'title_' . app()->getLocale()},
            // $circulation->resource->title_en?? ($circulation->resource->title_ku?? $circulation->resource->title_ar ),
            class_basename($circulation->resource->resourceable_type),
            $circulation->locked,
            $circulation->shelf_number,
            $circulation->storage_location,
            $circulation->barcode
        ];
    }return [];
    }
}
