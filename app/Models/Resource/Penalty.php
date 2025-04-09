<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Penalty extends BaseModel
{
    use HasFactory;
    protected $table="penalties";
    public $timestamps = false;
    protected $fillable = [
        'circulation_id',
        'how_much_per_day',
        'updated_by',
        'total_penalty_amount',
        'is_paid',
        'days_overdue'
    ];
    

    public function circulation(){
        return $this->belongsTo(Circulation::class,'circulation_id','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

 }
