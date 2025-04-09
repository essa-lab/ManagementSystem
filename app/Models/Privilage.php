<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Privilage extends Model
{
    use HasFactory;

    protected $fillable = [
        'privilage_name',
    ];



    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_privilages',
            'privilage_id',
            'user_id'
        );
    }
}
