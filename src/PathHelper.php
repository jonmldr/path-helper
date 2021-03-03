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

        $components = [];
        foreach ($arguments as $component) {
            $components = [...$components, ...array_filter(explode('/', $component))];
        }

        /*
         * Resolve resolvable parent directory references (..) & remove current directory references (.)
         */
        foreach ($components as $index => $component) {
            if ($component === '.') {
                unset($components[$index]);

                continue;
            }

            if ($component !== '..') {
                continue;
            }

            // Do not resolve reference if no previous component has been found
            $previousIndex = self::findPreviousNonReferencingComponent($components, $index);
            if ($previousIndex === null) {
                continue;
            }

            unset($components[$previousIndex], $components[$index]);
        }

        $path = implode('/', $components);

        // Leading slash
        if (strpos($arguments[0], '/') === 0) {
            $path = '/' . $path;
        }

        // Trailing slash
        $lastComponent = array_slice($arguments, -1)[0];
        if (substr($lastComponent, -1) === '/') {
            $path .= '/';
        }

        return $path;
    }

    private static function findPreviousNonReferencingComponent(array $components, int $startIndex): ?int
    {
        // Iterate through components in reverse order
        for ($i = count($components) - 1; $i >= 0; --$i) {
            // Continue if index is not before the start index
            if ($i >= $startIndex) {
                continue;
            }

            // Continue if element has been deleted
            if (array_key_exists($i, $components) === false) {
                continue;
            }

            // Continue if element is a reference
            $component = $components[$i];
            if ($component === '..' || $component === '.') {
                continue;
            }

            return $i;
        }

        return null;
    }
}
