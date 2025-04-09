<?php

namespace Database\Seeders;

use App\Models\Library;
use App\Models\Resource\Circulation;
use App\Models\Resource\LibrarySetting;
use App\Models\Resource\Penalty;
use App\Models\Resource\PenaltyValue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

    //    $libraryr =  Library::create([
    //         'name_ar'=>'Central',
    //         'name_en'=>'Central',
    //         'name_ku'=>'Central',
    //         'location'=>'Central',
    //         'logo'=>'logo'
    //     ]);
    //     $superAdmin = User::create([
    //         'name' => 'Issa',
    //         'email' => 'issath.arar@gmail.com',
    //         'password'=>'123123123',
    //         'role'=>'super_admin',
    //         'library_id'=>$libraryr->id,
    //     ]);
        
        // Resource::whereNull('language_id')->update(['language_id' => 32]);
        PenaltyValue::create([
'created_by'=>1,
'amount'=>3000,
'created_at'=>'2021-09-01 00:00:00'
        ]);
     

        // $Circulation = Circulation::find(1);

        // $Circulation->borrow_date = Carbon::parse($Circulation->borrow_date)->subDays(1);
        // $Circulation->due_date = Carbon::parse($Circulation->due_date)->subDays(1);
        // $Circulation->status = 'borrowed';
        // $Circulation->save();

        // $Penalty = Penalty::create([
        //     'circulation_id'=>$Circulation->id,
        //     'is_paid'=>0,
        //     'how_much_per_day'=>3000,
        //     'days_overdue'=>1,
        //     'total_penalty_amount'=>3000

        // ]);

        // $Penalty2 = Penalty::create([
        //     'circulation_id'=>$Circulation->id,
        //     'is_paid'=>0,
        //     'how_much_per_day'=>3000,
        //     'days_overdue'=>2,
        //     'total_penalty_amount'=>6000

        // ]);

        // $Penalty3 = Penalty::create([
        //     'circulation_id'=>$Circulation->id,
        //     'is_paid'=>0,
        //     'how_much_per_day'=>3000,
        //     'days_overdue'=>3,
        //     'total_penalty_amount'=>9000

        // ]);


        // $Circulation = Circulation::find(10);

        // $Circulation->borrow_date = Carbon::parse($Circulation->borrow_date)->subDays(5);
        // $Circulation->due_date = Carbon::parse($Circulation->due_date)->subDays(10);
        // $Circulation->status = 'overdue';
        // $Circulation->save();

        // $Penalty = Penalty::create([
        //     'circulation_id'=>$Circulation->id,
        //     'is_paid'=>0,
        //     'how_much_per_day'=>3000,
        //     'days_overdue'=>1,
        //     'total_penalty_amount'=>3000

        // ]);

     

    }
}
