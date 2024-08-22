<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
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
    protected $with = [];

        /**
     * Get the name that owns the description.
     */
    public function name()
    {
        return $this->belongsTo('Models\Name');
    }  
}
