<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use App\Models\Library;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ResourceCopy extends BaseModel
{
    use HasFactory;

    protected $table = "resource_copies";
    protected $fillable = [
        'barcode',
        'copy_number',
        'resource_id',
        'shelf_number',
        'storage_location',
        'status',
    ];



    public function resource()
    {
        return $this->belongsTo(Resource::class,'resource_id');
    }


}
