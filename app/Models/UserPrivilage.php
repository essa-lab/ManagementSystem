<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserPrivilage extends Model
{
    protected $table = 'user_privilages';

    protected $fillable = ['user_id', 'privilage_id'];

}
