<?php

declare(strict_types=1);

namespace App\Actions\Website;

class UpdateNestedSettings
{
    public function handle(&$current, array $new): bool
    {
        $changed = false;

        foreach ($new as $key => $value) {
            if (is_array($value) && isset($current->$key) && is_object($current->$key)) {
                $subChanged = $this->handle($current->$key, $value);
                $changed = $changed || $subChanged;
            } else if (isset($current->$key) && $current->$key !== $value) {
                $current->$key = $value;
                $changed = true;
            }
        }

        return $changed;
    }
}
