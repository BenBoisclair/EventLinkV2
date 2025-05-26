<?php

namespace App\Casts;

use App\DTOs\WebsiteSettings;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class WebsiteSettingsCast implements CastsAttributes
{
    public function get(Model $model, string $key, $value, array $attributes)
    {
        $array = is_array($value) ? $value : json_decode($value, true) ?? [];
        return new WebsiteSettings($array);
    }

    public function set(Model $model, string $key, $value, array $attributes)
    {
        if ($value instanceof WebsiteSettings) {
            return json_encode($value->toArray());
        }
        if (is_array($value)) {
            return json_encode($value);
        }
        return json_encode([]);
    }
}
