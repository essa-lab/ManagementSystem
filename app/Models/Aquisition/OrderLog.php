<?php

namespace App\Models\Aquisition;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OrderLog extends BaseModel
{
    use HasFactory;

    protected $table = "order_status_logs";

    public $timestamps = false;


    protected $fillable = [
        'purchase_order_id',
        'changed_by',
        'status'
    ];


    public function changedBy()
    {
        return $this->belongTo(User::class,'changed_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'purchase_order_id');
    }
   

}
