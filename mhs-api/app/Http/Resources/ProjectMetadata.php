<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMetadata extends JsonResource
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
			'name_id' => $this->name_id,
			'project_id' => $this->project_id,
            'notes' => $this->notes,
			'public' => $this->public
        ];
    }
}