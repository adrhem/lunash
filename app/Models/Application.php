<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\EmbedsMany;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

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
        'compose_file',
        'services',
    ];

    public function services(): EmbedsMany
    {
        return $this->embedsMany(Service::class);
    }

    public function containers(): EmbedsMany
    {
        return $this->embedsMany(Container::class);
    }

    public function parsedYamlServices(): Attribute
    {
        return Attribute::make(
            get: fn($_, $attributes) => !empty($attributes['compose_file']) ? self::parseComposeFile($attributes['compose_file']) : [],
        );
    }

    public static function parseComposeFile(string $composeFile): array
    {
        try {
            $parsed = Yaml::parseFile($composeFile);
            if (!is_array($parsed['services']) || empty($parsed['services']) || !is_array($parsed['services'])) {
                return [];
            }

            return array_map(function (array $config, string $name) {
                [$image, $tag] = explode(':', trim($config['image'], " \n\r\t\v\0\"")) + [1 => 'latest'];
                return [
                    'name' => $config['container_name'] ?? $name,
                    'image' => $image,
                    'tag' => $tag,
                ];
            }, $parsed['services'], array_keys($parsed['services']));
        } catch (ParseException $e) {
            return [];
        }
    }

    protected static function booted(): void
    {
        static::created(function (Application $application) {
            $application->updateServices();
        });
        static::updated(function (Application $application) {
            $application->updateServices();
        });
        static::saved(function (Application $application) {
            $application->updateServices();
        });
    }

    private function updateServices(): void
    {
        $this->services()->delete();
        foreach ($this->parsedYamlServices as $service) {
            $this->services()->performInsert(new Service($service));
        }
    }
}
