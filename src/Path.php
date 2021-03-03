<?php

declare(strict_types=1);

namespace Jon;

abstract class Path
{
    /**
     * Alias for PathHelper::path().
     */
    public static function combine(string ...$args): string
    {
        return PathHelper::path(...func_get_args());
    }
}
