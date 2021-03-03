<?php

declare(strict_types=1);

namespace Jon;

/**
 * Helper class for creating relative paths.
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

        /*
         * Resolve parent references (..)
         * & remove current dir reference (.)
         */
        foreach ($parts as $index => $part) {
            if ($part === '.') {
                unset($parts[$index]);

                continue;
            }

            if ($part !== '..') {
                continue;
            }

            $parentIndex = self::findPreviousNonReferencingIndex($parts, $index);
            if ($parentIndex === null) {
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

    private static function findPreviousNonReferencingIndex(array $parts, int $startIndex): ?int
    {
        for ($i = count($parts) - 1; $i >= 0; --$i) {
            // Continue if index is not before the start index
            if ($i >= $startIndex) {
                continue;
            }

            // Continue if element has been deleted
            if (array_key_exists($i, $parts) === false) {
                continue;
            }

            // Continue if element is a reference
            $part = $parts[$i];
            if ($part === '..' || $part === '.') {
                continue;
            }

            return $i;
        }

        return null;
    }
}
