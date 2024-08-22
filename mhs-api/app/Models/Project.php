<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
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
    protected $with = [
        //'subjects',
        //'names',
	//'documents',
    ];

    /**
     * The subjects that belong to the project.
     */
    public function subjects()
    {
        return $this->belongsToMany('Models\Subject');
    }

	    /**
     * The subjects that belong to the project.
     */
    public function documents()
    {
        return $this->hasMany('Models\Document');
    }


    /**
     * The names that belong to the project.
     */
    public function names()
    {
        return $this->belongsToMany('Models\Name');
    }    

    /**
     * Get the lists for the project.
     */
    public function lists()
    {
        return $this->hasMany('Models\ProjectList');
    }    
}
