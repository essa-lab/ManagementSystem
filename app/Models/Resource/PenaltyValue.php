<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PenaltyValue extends BaseModel
{
    use HasFactory;
    public $timestamps = false;

    protected $table="penalty_values";
    protected $fillable = [
        'created_by',
        'amount',
        'created_at'
    ];

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }

  
}
