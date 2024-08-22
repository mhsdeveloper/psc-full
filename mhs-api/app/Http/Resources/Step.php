<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Step extends JsonResource
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
            'name' => $this->name,
            'order' => $this->order,
            'project_id' => $this->project_id,
            'short_name' => $this->short_name,
            'description' => $this->description,
			'color' => $this->color,
            'status' => $this->whenPivotLoaded('document_step', function () {
                return $this->pivot->status;
            }),
			'username' => $this->whenPivotLoaded('document_step', function () {
                return $this->pivot->username;
            }),
			'document_step_id' => $this->whenPivotLoaded('document_step', function () {
                return $this->pivot->id;
            })

        ];
    }
}