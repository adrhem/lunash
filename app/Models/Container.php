<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Container extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'container_id',
        'image',
        'name',
        'status',
        'command',
    ];
}
