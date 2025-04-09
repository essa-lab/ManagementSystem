<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\LibraryResource;
use App\Http\Resources\PatronResource;
use App\Http\Resources\UserResource;
use App\Models\Library;
use App\Traits\FilePath;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TrackInventoryResource extends JsonResource
{
    use FilePath;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // info($this->filePath());
        return [
            'id' => $this->id,
            'name_ku'=>$this->name_ku,
            'name_ar'=>$this->name_ar,
            'name_en'=>$this->name_en,

            'name'=> $this->{'name_' . app()->getLocale()},
            'location' => $this->location,
            'logo'=>$this->logo ? Storage::url($this->logo) : null,

            'borrowed_count'=>$this->borrowed_count,
            'reserved_count'=>$this->reserved_count,
            'available_count'=>$this->available_count,

        ];
    }
}
