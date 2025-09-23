<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\EmbedsMany;

class Application extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status',
        'services_count',
        'has_updates',
        'compose_file',
        'services',
    ];

    public function hasUpdatesText(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->has_updates ? 'Yes' : 'No'
        );
    }

    public function services(): EmbedsMany
    {
        return $this->embedsMany(Service::class);
    }
}
