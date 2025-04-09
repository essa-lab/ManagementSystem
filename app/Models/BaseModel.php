<?php

namespace App\Models;

use App\Models\Resource\Resource;
use App\Observers\ActivityLogObserver;
use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class BaseModel extends Model
{
    protected static function boot() {
        parent::boot();
        static::observe(ActivityLogObserver::class);
    }
}