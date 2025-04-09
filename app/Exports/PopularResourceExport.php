<?php

namespace App\Exports;

use App\Helper\Authorize;
use App\Models\Resource\Circulation;
use App\Models\Resource\ResourceCopy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PopularResourceExport implements FromCollection , WithHeadings, WithMapping ,WithStyles
{

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $user = Auth::user();
        return  Circulation::select(
            'resources.title_en','resources.title_ku','resources.title_ar' ,'resources.resourceable_type',
            DB::raw('COUNT(circulations.id) as circulation_count')
        )
        ->join('resource_copies', 'circulations.resource_copy_id', '=', 'resource_copies.id')
        ->join('resources', 'resource_copies.resource_id', '=', 'resources.id') 
        ->when(!Authorize::isSuperAdmin($user), function ($query) use ($user) {
            $query->where('resources.library_id', $user->library_id); 
        })
        ->groupBy('resources.id', 'resources.title_en','resources.title_ku','resources.title_ar','resources.resourceable_type')
        ->orderByDesc('circulation_count')
        ->limit(10)
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
                   __('messages.circulated'),
                    
        ];
    }
    
    public function map($circulation): array
    {
        return [
            $circulation->{'title_' . app()->getLocale()},
            class_basename($circulation->resourceable_type),
            $circulation->circulation_count,

        ];
    }
}
