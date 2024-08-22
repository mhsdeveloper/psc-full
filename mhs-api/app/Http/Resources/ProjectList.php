<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Name as NameResource;
use App\Http\Resources\Subject as SubjectResource;

class ProjectList extends JsonResource
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
            'project_id' => $this->project_id,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'subjects' => SubjectResource::collection($this->whenLoaded('subjects')),
            'names' => NameResource::collection($this->whenLoaded('names'))
        ];
    }
}