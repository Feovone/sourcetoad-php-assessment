<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question3\Domain;

use InvalidArgumentException;

final class Item
{
    public function __construct(
        private readonly int    $id,
        private readonly string $name,
        private readonly Money  $price,
    ) {
        if (trim($name) === '') {
            throw new InvalidArgumentException('Item name cannot be empty.');
        }
        if ($price->cents() <= 0) {
            throw new InvalidArgumentException('Item price must be positive.');
        }
    }

    public function id(): int { return $this->id; }
    public function name(): string { return $this->name; }
    public function price(): Money { return $this->price; }
}
