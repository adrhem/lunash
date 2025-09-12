<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DockerManifest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'image',
        'tag',
        'manifest',
    ];

    protected $casts = [
        'manifest' => 'array',
    ];
}
