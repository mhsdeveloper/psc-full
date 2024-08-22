<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Subject extends JsonResource
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
            'subject_name' => $this->subject_name,
            'display_name' => $this->display_name,
            'staff_notes' => $this->staff_notes,
            'keywords' => $this->keywords,
            'loc' => $this->loc,
            'children' => $this->whenLoaded('descendants')
        ];
    }
}