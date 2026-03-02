<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question1;

final class ArrayPrinter
{
    public static function printRecursive(array $data, string $prefix = ''): void
    {
        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                self::printRecursive($value, $path);
                continue;
            }

            $display = match (true) {
                is_null($value) => '(null)',
                is_bool($value) => $value ? 'true' : 'false',
                default         => (string) $value,
            };

            echo "{$path}: {$display}\n";
        }
    }

    public static function printIterative(array $data, string $prefix = ''): void
    {
        $stack = [[$data, $prefix]];

        while (!empty($stack)) {
            [$current, $parentKey] = array_pop($stack);

            $keys = array_keys($current);
            for ($i = count($keys) - 1; $i >= 0; $i--) {
                $key   = $keys[$i];
                $value = $current[$key];
                $path  = $parentKey === '' ? (string) $key : "{$parentKey}.{$key}";

                if (is_array($value)) {
                    $stack[] = [$value, $path];
                } else {
                    $display = match (true) {
                        is_null($value) => '(null)',
                        is_bool($value) => $value ? 'true' : 'false',
                        default         => (string) $value,
                    };
                    echo "{$path}: {$display}\n";
                }
            }
        }
    }
}
