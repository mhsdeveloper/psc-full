<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Link extends JsonResource
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
            'type' => $this->type,
            'authority' => $this->authority,
            'authority_id' => $this->authority_id,
            'display_title' => $this->display_title,
            'url' => $this->url,
            'notes' => $this->notes
        ];
    }
}