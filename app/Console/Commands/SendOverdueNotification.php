<?php

namespace App\Console\Commands;

use App\Models\Resource\Circulation;
use App\Models\Resource\LibrarySetting;
use App\Notifications\DueSoonNotification;
use App\Notifications\OverdueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendOverdueNotification extends Command
{
    protected $signature = 'notify:due-overdue';
    protected $description = 'Send notifications for soon-to-be-due and overdue books.';

    public function handle()
    {
        // $notificationHour = LibrarySetting::value('schedular_time') ?? '09:00:00';
        // $runTime = Carbon::today()->setTimeFromTimeString($notificationHour);
        // if (now()->lt($runTime)) {
        //     $this->info("Skipping execution. Will run at $runTime.");
        //     return;
        // }

        $tomorrow = Carbon::tomorrow()->startOfDay();
        $today = Carbon::today()->startOfDay();
        $dueSoonCirculations = Circulation::where('status','borrowed')->whereDate('due_date', $tomorrow)->with('patron')->get();
        $overdueCirculations = Circulation::whereDate('due_date', '<', $today)
            ->where('status', 'overdue')
            ->with('patron')
            ->get();
        foreach ($dueSoonCirculations as $circulation) {
            if ($circulation->patron && $circulation->patron->email) {
                $circulation->patron->notify(new DueSoonNotification($circulation));
                // Mail::to($circulation->patron->email)->send(new DueSoonNotification($circulation));
                $this->info("Sent due soon notification to " . $circulation->patron->email);
            }
        }
        foreach ($overdueCirculations as $circulation) {
            if ($circulation->patron && $circulation->patron->email) {
                $circulation->patron->notify(new OverdueNotification($circulation));
                $this->info("Sent overdue notification to " . $circulation->patron->email);
            }
        }
        $this->info("Due and Overdue notifications sent successfully.");
    }
}

