<?php
namespace App\Http\Controllers\Admin;

use App\Exports\AvailableResourceExport;
use App\Exports\BorrowResourceExport;
use App\Exports\DamagedResourceExport;
use App\Exports\LostResourceExport;
use App\Exports\OverdueResourceExport;
use App\Exports\PopularResourceExport;
use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\GenerateReportRequest;
use App\Http\Requests\SendEmailRequest;
use App\Models\Resource\Circulation;
use App\Models\Resource\LibrarySetting;
use App\Models\Resource\ResourceCopy;
use App\Notifications\ReturnResourceEmail;
use App\Notifications\SeasonEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ExportController extends Controller
{
    public function exportResources(GenerateReportRequest $request)
    {


        $data = $request->validated();
        $user = Auth::user();
        if($data['format_type']=='pdf'){

            if($data['report_type']=='overdue'){

                $circulation = Circulation::where('status', 'overdue')->whereBetween('due_date', [$data['from'], $data['to']])

                ->with(['penalties', 'resourceCopy.resource' => function($query) use ($user) {
                    if (!Authorize::isSuperAdmin($user)) {
                        $query->where('resources.library_id', $user->library_id);
                    }
                }, 'patron'])
                ->get();


                $pdf = Pdf::loadView('reports.overdue', compact('circulation'));
                return response()->streamDownload(
                    fn() => print ($pdf->output()),
                    'overdue.pdf',
                    ['Content-Type' => 'application/pdf']
                );
            }
            if($data['report_type']=='available'){

               $resourceCopy =  ResourceCopy::where('status', 'available')->whereBetween('updated_at', [$data['from'], $data['to']])
              ->with(['resource'=>function($query) use ($user){
                if (!Authorize::isSuperAdmin($user)) {
                    $query->where('library_id', $user->library_id); 
                }
              }])
              ->get();

              $pdf = Pdf::loadView('reports.available', compact('resourceCopy'));

                return response()->streamDownload(
                    fn() => print ($pdf->output()),
                    'available.pdf',
                    ['Content-Type' => 'application/pdf']
                ); 
            }
            if($data['report_type']=='popular'){//
                $circulation = Circulation::select(
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



                $pdf = Pdf::loadView('reports.popular', compact('circulation'));
                return response()->streamDownload(
                    fn() => print ($pdf->output()),
                    'popular.pdf',
                    ['Content-Type' => 'application/pdf']
                );            }
            if($data['report_type']=='borrow'){
                $circulation = Circulation::where('status', 'borrowed')->whereBetween('borrow_date', [$data['from'], $data['to']])
            ->with(['penalties','resourceCopy.resource' => function($query) use ($user) {
                if (!Authorize::isSuperAdmin($user)) {
                    $query->where('library_id', $user->library_id); 
                }
            }, 'patron'])
            ->get();


            $pdf = Pdf::loadView('reports.borrow', compact('circulation'));

            return response()->streamDownload(
                fn() => print ($pdf->output()),
                'borrow.pdf',
                ['Content-Type' => 'application/pdf']
            ); 
            }

            if($data['report_type']=='lost'){

                $resourceCopy =  ResourceCopy::where('status', 'lost')->whereBetween('updated_at', [$data['from'], $data['to']])
               ->with(['resource' => function($query) use ($user) {
                if (!Authorize::isSuperAdmin($user)) {
                    $query->where('library_id', $user->library_id); 
                }
            }])
               ->get();
 
               $pdf = Pdf::loadView('reports.lost', compact('resourceCopy'));
 
                 return response()->streamDownload(
                     fn() => print ($pdf->output()),
                     'lost.pdf',
                     ['Content-Type' => 'application/pdf']
                 ); 
             }
             if($data['report_type']=='damaged'){

                $resourceCopy =  ResourceCopy::where('status', 'damaged')->whereBetween('updated_at', [$data['from'], $data['to']])
               ->with(['resource' => function($query) use ($user) {
                if (!Authorize::isSuperAdmin($user)) {
                    $query->where('library_id', $user->library_id); 
                }
            }])
               ->get();
 
               $pdf = Pdf::loadView('reports.damaged', compact('resourceCopy'));
 
                 return response()->streamDownload(
                     fn() => print ($pdf->output()),
                     'damaged.pdf',
                     ['Content-Type' => 'application/pdf']
                 ); 
             }
            

        }else{
            if($data['report_type']=='overdue'){              
                return Excel::download(new OverdueResourceExport($data['from'],$data['to']), 'overdue_resources.xlsx');
            }
            if($data['report_type']=='available'){
                return Excel::download(new AvailableResourceExport($data['from'],$data['to']), 'available_resources.xlsx');
            }
            if($data['report_type']=='popular'){//
                return Excel::download(new PopularResourceExport, 'popular_resources.xlsx');
            }
            if($data['report_type']=='borrow'){
                return Excel::download(new BorrowResourceExport($data['from'],$data['to']), 'borrow_resources.xlsx');
            }
            if($data['report_type']=='lost'){
                return Excel::download(new LostResourceExport($data['from'],$data['to']), 'lost_resources.xlsx');
            }
            if($data['report_type']=='damaged'){
                return Excel::download(new DamagedResourceExport($data['from'],$data['to']), 'damaged_resources.xlsx');
            }
        }
        
    }

    public function season(){
        $lastSeasonEnd = LibrarySetting::first()->value('last_season_end')??'2025-01-01';
        $user = Auth::user();

        $borrow = Circulation::where('status', 'borrowed')->where('borrow_date', '>',$lastSeasonEnd)
            ->with(['penalties','resourceCopy.resource' => function($query) use ($user) {
                if (!Authorize::isSuperAdmin($user)) {
                    $query->where('library_id', $user->library_id); 
                }
            }, 'patron'])
            ->get();

            


              $overdue = Circulation::where('status', 'overdue')->where('due_date', '>',$lastSeasonEnd)

                ->with(['penalties', 'resourceCopy.resource' => function($query) use ($user) {
                    if (!Authorize::isSuperAdmin($user)) {
                        $query->where('resources.library_id', $user->library_id);
                    }
                }, 'patron'])
                ->get();



                $pdf = Pdf::loadView('reports.season', compact('overdue','borrow'));

                $fileName = 'season_report_' . $lastSeasonEnd . '.pdf';
                $filePath = 'reports/' . $fileName;
            
                // Store PDF in storage/app/public/reports/
                Storage::disk('public')->put($filePath, $pdf->output());
                foreach($borrow as $p){
                    info($p->patron->name);
                    Notification::route('mail', $p->patron->email)
                    ->notify(new ReturnResourceEmail($p->patron->name));
                }
        
                foreach($overdue as $p){
                    Notification::route('mail', $p->patron->email)
                    ->notify(new ReturnResourceEmail($p->patron->name));
                }
                
                 return response()->streamDownload(
                     fn() => print ($pdf->output()),
                     'seasoned-report.pdf',
                     ['Content-Type' => 'application/pdf']
                 ); 


    }


    //send email to admins
    //send email to patrons

    public function sendEmailToHeads(SendEmailRequest $request){
        $data = $request->validated();
        $lastSeasonEnd = LibrarySetting::first()->value('last_season_end')??'2025-01-01';


        $fileName = 'season_report_' . $lastSeasonEnd . '.pdf';
                $filePath = 'reports/' . $fileName;

        foreach($data['emails'] as $email){
            Notification::route('mail', $email)
            ->notify(new SeasonEmail($filePath));
    
        }
      
        return ApiResponse::sendResponse('Done');

    }

   
}
