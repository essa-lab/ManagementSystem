<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model {
    protected $fillable = [
        'loggable_type', 'loggable_id', 'user_id', 'action',
        'old_values', 'new_values', 'changes',
        'ip_address', 'user_agent', 'performed_at'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
        'performed_at' => 'datetime',
    ];

    public function loggable(): MorphTo {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
