<?php

namespace Models;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use NodeTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */       
    protected $guarded = [];

    /**
     * The fields that shouldn't be shown
     *
     * @var array
     */    
    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
        '_lft',
        '_rgt',
        'parent_id'
    ];

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $with = ['links'];

    /**
     * The projects that belong to the subject.
     */
    public function projects()
    {
        return $this->belongsToMany('Models\Project');
    }

    /**
     * Get all of the subject's links.
     */
    public function links()
    {
        return $this->morphMany('Models\Link', 'linkable');
    }    
}
