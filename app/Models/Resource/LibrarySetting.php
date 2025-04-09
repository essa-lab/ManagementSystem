<?php

namespace App\Models\Resource;

use App\Models\BaseModel;
use App\Models\Library;
use App\Models\Resource\Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LibrarySetting extends BaseModel
{
    use HasFactory;

    protected $table = "global_settings";
    public $timestamps = false;
    protected $fillable = [
        'self_registeration',
        'scheduler_time',
    ];



    public function library()
    {
        return $this->belongsTo(Library::class,'library_id');
    }


}
