<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use stdClass;

class Service extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'image_id',
        'name',
        'repository',
        'tag',
        'platform',
        'size',
    ];

    public function repositoryJson(): Attribute
    {
        return Attribute::make(
            get: fn() => json_encode([
                'label' => $this->repository,
                'url' => $this->repository_url,
            ])
        );
    }

    public function repositoryUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => "https://hub.docker.com/layers/{$this->repository}/latest/images/sha256-{$this->sha256}"
        );
    }

    public function sha256(): Attribute
    {
        return Attribute::make(
            get: fn() => str_replace('sha256:', '', $this->image_id)
        );
    }
}
