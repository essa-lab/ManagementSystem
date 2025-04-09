<?php

namespace App\Models\Resource;

use App\Models\Patron;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CirculationLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'circulation_id',
        'action_date',
        'action_by',
        'status'
    ];
    protected $casts=[
        'action_date'=>'date'
    ];
    public function circulation(){
        return $this->belongsTo(Circulation::class,'circulation_id','id');
    }
    public function actionBy(){
        return $this->belongsTo(User::class,'action_by','id');
    }

    
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

}
