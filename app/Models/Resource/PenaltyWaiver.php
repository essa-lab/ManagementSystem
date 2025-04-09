<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PenaltyWaiver extends BaseModel
{
    use HasFactory;
    protected $table = "penalty_waivers";
    protected $fillable = [
        'penlaty_id',
        'waived_by',
        'reason',
    ];

    public function penalty(){
        return $this->belongsTo(Penalty::class,'penlaty_id');
    }
    public function waivedBy(){
        return $this->belongsTo(User::class,'waived_by');
    }


}
