<?php

declare(strict_types=1);

namespace Jon;

/**
 * Helper class for creating relative paths
 */
abstract class PathHelper
{
    public static function path(string ...$args): string
    {
        $arguments = func_get_args();
        if (count($arguments) === 0) {
            return '';
        }

        $parts = [];
        foreach ($arguments as $part) {
            $parts = [...$parts, ...array_filter(explode('/', $part))];
        }

        // Resolve parent references (..) & remove current dir reference (.)
        foreach ($parts as $index => $part) {
            if ($part === '.') {
                unset($parts[$index]);

                continue;
            }

            if ($part !== '..') {
                continue;
            }

            $parentIndex = $index - 1;
            if ($parentIndex <= 0) {
                continue;
            }

            unset($parts[$parentIndex], $parts[$index]);
        }

        $path = implode('/', $parts);

        // Leading slash
        if (strpos($arguments[0], '/') === 0) {
            $path = '/' . $path;
        }

        // Trailing slash
        $lastPart = array_slice($arguments, -1)[0];
        if (substr($lastPart, -1) === '/') {
            $path .= '/';
        }

        return $path;
    }
}
