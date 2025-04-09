<?php
namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogObserver {
    public function created($model) {

        $attributes = $model->getAttributes();

        if ($model instanceof User || $model instanceof Patron) {
            unset($attributes['password']);
        }

        $this->logActivity($model, 'created', [], $attributes);
    }

    public function updated($model) {
        $original = $model->getOriginal();
    $changes = $model->getChanges();

    if ($model instanceof User || $model instanceof Patron) {
        unset($original['password'], $changes['password']); 
    }

        $this->logActivity($model, 'updated', $original, $changes);
    }

    public function deleted($model) {
        $this->logActivity($model, 'deleted', $model->getAttributes(), []);
    }

    private function logActivity($model, $action, $oldValues, $newValues) {
        ActivityLog::create([
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'user_id' => auth()->id() ?? null,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => array_diff_assoc($newValues, $oldValues),
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'performed_at' => now(),
        ]);
    }
}
