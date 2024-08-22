<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Step as StepResource;

class Document extends JsonResource
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
            'filename' => $this->filename,
            'project_id' => $this->project_id,
            'notes' => $this->notes,
            'authors' => $this->authors,
            'recipients' => $this->recipients,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
			'teaser' => $this->teaser,
			'title' => $this->title,
            'published' => $this->published,
            'publish_date' => $this->publish_date,
            'checked_out' => $this->checked_out,
            'checked_outin_by' => $this->checked_outin_by,
            'checked_outin_date' => $this->checked_outin_date,
            'steps' => StepResource::collection($this->steps->sortBy("order"))
        ];
    }
}