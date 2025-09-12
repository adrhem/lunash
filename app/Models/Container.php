<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'state',
        'command',
    ];

    public final static array $states = ["created", "running", "paused", "restarting", "exited", "removing", "dead"];

    protected function command(): Attribute
    {
        return Attribute::make(
            // Remove surrounding quotes and whitespace
            set: fn($value) => trim($value, " \n\r\t\v\0\"")
        );
    }
}
