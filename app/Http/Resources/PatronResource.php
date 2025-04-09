<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatronResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'internal_identifier'=>$this->internal_identifier,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'occupation' => $this->occupation,
            'address'=>$this->address,
            'remember_token'=>$this->remember_token,

            'status'=>$this->status,
            'created_at' => $this->created_at,
            'university'=>$this->university,
            'college'=>$this->college,
            'verified'=>$this->verified,
            'verified_at'=>$this->verified_at,
            'updated_at' => $this->updated_at,
            'locale'=>$this->locale,
            'is_deleted' => $this->deleted_at ? true : false,
        ];
    }
}
