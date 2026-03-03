<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question2;

final class NestedSorter
{
    public static function findValue(array $array, string $key, int $depth = 0): mixed
    {
        if ($depth >= 64) {
            return null;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach ($array as $value) {
            if (is_array($value)) {
                $found = self::findValue($value, $key, $depth + 1);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    public static function sortByKeys(array &$data, string|array $keys): void
    {
        $keys = (array) $keys;

        usort($data, function (array $a, array $b) use ($keys): int {
            foreach ($keys as $key) {
                $cmp = self::findValue($a, $key) <=> self::findValue($b, $key);
                if ($cmp !== 0) {
                    return $cmp;
                }
            }
            return 0;
        });
    }
}
