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
        'name',
        'image',
        'command',
        'service',
        'status'
    ];

    protected function command(): Attribute
    {
        return Attribute::make(
            // Remove surrounding quotes and whitespace
            set: fn($value) => trim($value, " \n\r\t\v\0\"")
        );
    }
}
