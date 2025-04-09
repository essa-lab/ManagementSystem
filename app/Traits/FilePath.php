<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FilePath {
    public function filePath($key) {
        if($this[$key] && str_starts_with($this[$key], 'http')) {
            return $this[$key];
        }
        return $this[$key] ? Storage::url($this[$key]) : null;
    }
}
