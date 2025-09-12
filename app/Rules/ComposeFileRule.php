<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;


class ComposeFileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!file_exists($value)) {
            $fail('The :attribute file does not exist.');
            return;
        }

        if (!in_array(pathinfo($value, PATHINFO_EXTENSION), ['yml', 'yaml'])) {
            $fail('The :attribute must have a .yml or .yaml extension.');
        }

        try {
            $parsed = Yaml::parseFile($value);
            if (!is_array($parsed['services']) || empty($parsed['services']) || !is_array($parsed['services'])) {
                $fail('The :attribute file does not appear to be a valid Docker Compose file (missing "services" section).');
            }
        } catch (ParseException $e) {
            $fail('The :attribute file is not a valid YAML file: ' . $e->getMessage());
        }
    }
}
