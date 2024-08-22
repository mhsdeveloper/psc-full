<?php

namespace App\Http\Resources;

use App\Http\Resources\Link as LinkResource;
use App\Http\Resources\ProjectMetadata as ProjectMetadataResource;
use App\Http\Resources\Note as NoteResource;
use App\Http\Resources\Alias as AliasResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Name extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function XXXXXXtoArray($request)
    // {
    //     return [
    //         'id' => $this->id,
    //         'family_name' => $this->family_name,
    //         'given_name' => $this->given_name,
    //         'middle_name' => $this->middle_name,
    //         'maiden_name' => $this->maiden_name,
    //         'suffix' => $this->suffix,
    //         'date_of_birth' => $this->date_of_birth,
    //         'date_of_death' => $this->date_of_death,
    //         'public_notes' => $this->public_notes,
    //         'staff_notes' => $this->staff_notes,
    //         'identifier' => $this->identifier,
    //         'first_mention' => $this->first_mention,
    //         'verified' => $this->verified,
    //         'name_key' => $this->name_key,
    //         'variants' => $this->variants,
    //         'professions' => $this->professions,
    //         'title' => $this->title,
    //         'birth_ca' => $this->birth_ca,
    //         'death_ca' => $this->death_ca,
    //         'birth_era' => $this->birth_era,
    //         'death_era' => $this->death_era,
    //         'created_at' => $this->created_at,
    //         'updated_at' => $this->updated_at,
    //         'first_created_by' => $this->first_created_by,
    //         'aliases' => AliasResource::collection($this->whenLoaded('aliases')),
    //         'links' => LinkResource::collection($this->whenLoaded('links')),
    //         'notes' => NoteResource::collection($this->whenLoaded("notes")),
    //         'descriptions' => DescriptionResource::collection($this->whenLoaded("descriptions"))
    //     ];
    // }
}
