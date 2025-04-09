<?php

namespace App\Console\Commands;

use App\Models\Resource\Circulation;
use App\Models\Resource\LibrarySetting;
use App\Models\Resource\Penalty;
use App\Models\Resource\PenaltyValue;
use App\Notifications\DueSoonNotification;
use App\Notifications\OverdueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculatePenaltyForCirculation extends Command
{
    protected $signature = 'penalty:calculate';
    protected $description = '';

    public function handle()
    {

        // $tomorrow = Carbon::tomorrow()->startOfDay();
        $today = Carbon::today()->startOfDay();

        $circulations = Circulation::where('status', 'overdue')->get();
        $newOverDueCirculation = Circulation::where('status','borrowed')->whereDate('due_date','<', $today)->get();

        $penaltyValue = PenaltyValue::latest()->first();

        
        foreach ($circulations as $circulation) {
            $penalty = $circulation->penalties->last();
            Penalty::create([
                'circulation_id'=>$circulation->id,
                'how_much_per_day'=>$penaltyValue->amount??100,
                'updated_by'=>null,
                'total_penalty_amount'=>$penalty->total_penalty_amount + $penaltyValue->amount??100,
                'is_paid'=>0,
                'days_overdue'=>$penalty->days_overdue+1

            ]);
        }
        foreach ($newOverDueCirculation as $circulation) {
            $circulation->status = 'overdue';
            $circulation->save();
            
            Penalty::create([
                'circulation_id'=>$circulation->id,
                'how_much_per_day'=>$penaltyValue->amount??100,
                'updated_by'=>null,
                'total_penalty_amount'=>$penaltyValue->amount??100,
                'is_paid'=>0,
                'days_overdue'=>1

            ]);
        }

        $this->info("Penalty has calcualted successfully.");
    }
}

