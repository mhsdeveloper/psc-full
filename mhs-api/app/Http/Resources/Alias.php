<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Alias extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'family_name' => $this->family_name,
            'given_name' => $this->given_name,
            'middle_name' => $this->middle_name,
            'maiden_name' => $this->maiden_name,
            'suffix' => $this->suffix,
            'title' => $this->title,
            'role' => $this->role,
            'type' => $this->type,
            'public_notes' => $this->public_notes,
            'staff_notes' => $this->staff_notes,
        ];
    }
}