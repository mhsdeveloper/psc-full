<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class ProjectList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lists';

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
        'updated_at'
    ];

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Get all of the subjects that are assigned this list.
     */
    public function subjects()
    {
        return $this->morphedByMany('Models\Subject', 'listable');
    }

    /**
     * Get all of the names that are assigned this list.
     */
    public function names()
    {
        return $this->morphedByMany('Models\Name', 'listable');
    }    
}
