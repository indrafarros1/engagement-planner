<?php

namespace App\Enums\Concerns;

trait HasLabel
{
    abstract public function label(): string;

    /** Pasangan [value => label] untuk Select/options. */
    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }

        return $out;
    }
}
