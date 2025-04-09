<?php

namespace App\Models\Aquisition;

use App\Models\ActivityLog;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class OrderItems extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "order_items";
    protected $fillable = [
        'purchase_order_id',
        'title',
        'type',
        'author',
        'isbn',
        'quantity',
        'price'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class,'purchase_order_id');
    }

   

}
