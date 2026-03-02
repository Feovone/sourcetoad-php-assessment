<?php

$data = [
    [
        'guest_id' => 177,
        'guest_type' => 'crew',
        'first_name' => 'Marco',
        'middle_name' => null,
        'last_name' => 'Burns',
        'gender' => 'M',
        'guest_booking' => [
            [
                'booking_number' => 20008683,
                'ship_code' => 'OST',
                'room_no' => 'A0073',
                'start_time' => 1438214400,
                'end_time' => 1483142400,
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 20009503,
                'status_id' => 2,
                'account_limit' => 0,
                'allow_charges' => true,
            ],
        ],
    ],
    [
        'guest_id' => 10000113,
        'guest_type' => 'crew',
        'first_name' => 'Bob Jr ',
        'middle_name' => 'Charles',
        'last_name' => 'Hemingway',
        'gender' => 'M',
        'guest_booking' => [
            [
                'booking_number' => 10000013,
                'room_no' => 'B0092',
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 10000522,
                'account_limit' => 300,
                'allow_charges' => true,
            ],
        ],
    ],
    [
        'guest_id' => 10000114,
        'guest_type' => 'crew',
        'first_name' => 'Al ',
        'middle_name' => 'Bert',
        'last_name' => 'Santiago',
        'gender' => 'M',
        'guest_booking' => [
            [
                'booking_number' => 10000014,
                'room_no' => 'A0018',
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 10000013,
                'account_limit' => 300,
                'allow_charges' => false,
            ],
        ],
    ],
    [
        'guest_id' => 10000115,
        'guest_type' => 'crew',
        'first_name' => 'Red ',
        'middle_name' => 'Ruby',
        'last_name' => 'Flowers ',
        'gender' => 'F',
        'guest_booking' => [
            [
                'booking_number' => 10000015,
                'room_no' => 'A0051',
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 10000519,
                'account_limit' => 300,
                'allow_charges' => true,
            ],
        ],
    ],
    [
        'guest_id' => 10000116,
        'guest_type' => 'crew',
        'first_name' => 'Ismael ',
        'middle_name' => 'Jean-Vital',
        'last_name' => 'Jammes',
        'gender' => 'M',
        'guest_booking' => [
            [
                'booking_number' => 10000016,
                'room_no' => 'A0023',
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 10000015,
                'account_limit' => 300,
                'allow_charges' => true,
            ],
        ],
    ],
];

// Approach 1 — Recursion: the call stack tracks depth for us automatically.
// Each nested array triggers a deeper call; leaves get printed.
function displayKeyValuesRecursive(array $data, string $prefix = ''): void
{
    foreach ($data as $key => $value) {
        $currentPath = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

        if (is_array($value)) {
            displayKeyValuesRecursive($value, $currentPath);
            continue;
        }

        $display = match (true) {
            is_null($value)  => '(null)',
            is_bool($value)  => $value ? 'true' : 'false',
            default          => (string) $value,
        };

        echo "{$currentPath}: {$display}\n";
    }
}

// Approach 2 — Iterative with explicit stack: avoids deep recursion limits,
// same depth-first traversal but we manage the stack ourselves.
function displayKeyValuesIterative(array $data, string $prefix = ''): void
{
    $stack = [[$data, $prefix]];

    while (!empty($stack)) {
        [$current, $parentKey] = array_pop($stack);

        $keys = array_keys($current);
        for ($i = count($keys) - 1; $i >= 0; $i--) {
            $key = $keys[$i];
            $value = $current[$key];

            $currentKey = $parentKey === '' ? (string) $key : $parentKey . '.' . $key;

            if (is_array($value)) {
                $stack[] = [$value, $currentKey];
            } else {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } elseif (is_null($value)) {
                    $value = '(null)';
                }

                echo $currentKey . ': ' . $value . PHP_EOL;
            }
        }
    }
}

echo "=== Approach 1: Recursive ===\n\n";
displayKeyValuesRecursive($data,'root');

echo "\n=== Approach 2: Iterative (with root prefix 'guests') ===\n\n";
displayKeyValuesIterative($data, 'root');
