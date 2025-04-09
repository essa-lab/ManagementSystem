<?php

namespace App\Models\Aquisition;

use App\Models\BaseModel;
use App\Models\Library;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends BaseModel
{
    use HasFactory;

    protected $table = "purchase_orders";

    protected $fillable = [
        'supplier_name',
        'contact_number',
        'po_number',
        'date',
        'status',
        'created_by',
        'approved_by',
        'note',
        'library_id'
    ];


    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class,'approved_by');
    }

    public function library()
    {
        return $this->belongsTo(Library::class,'library_id');
    }
   
   
    public function orderItems(){
        return $this->hasMany(OrderItems::class,'purchase_order_id');
    }

    public function logs(){
        return $this->hasMany(OrderLog::class,'purchase_order_id');
    }

    

}
