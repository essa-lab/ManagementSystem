<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use App\Models\Patron;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Review extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'rate',
        'review',
        'available',
        'resource_id',
        'patron_id'
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class,'resource_id');
    }

    public function patron()
    {
        return $this->belongsTo(Patron::class,'patron_id','id');
    }

}
