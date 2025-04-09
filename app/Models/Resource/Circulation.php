<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use App\Models\Patron;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Circulation extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'circulation_count',
        'return_date',
        'due_date',
        'borrow_date',
        'resource_copy_id',
        'patron_id',
        'status'
    ];

    protected $casts=[
        'due_date'=>'date',
        'borrow_date'=>'date'
    ];
    public function resourceCopy(){
        return $this->belongsTo(ResourceCopy::class,'resource_copy_id','id');
    }
    public function patron(){
        return $this->belongsTo(Patron::class,'patron_id','id');
    }
    public function logs(){
        return $this->hasMany(CirculationLog::class,'circulation_id');
    }
    public function penalties(){
        return $this->hasMany(Penalty::class,'circulation_id');
    }
    public function latestPenalty()
{
    return $this->hasOne(Penalty::class,'circulation_id')->latestOfMany();
}
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

}
