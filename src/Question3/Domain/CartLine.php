<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question3\Domain;

use InvalidArgumentException;

final class CartLine
{
    private int $quantity;

    public function __construct(
        private readonly Item $item,
        int $quantity,
    ) {
        $this->setQuantity($quantity);
    }

    public function item(): Item { return $this->item; }
    public function quantity(): int { return $this->quantity; }

    public function increaseQuantity(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Increase amount must be positive.');
        }
        $this->quantity += $amount;
    }

    public function setQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be positive.');
        }
        $this->quantity = $quantity;
    }

    public function lineTotal(): Money
    {
        return $this->item->price()->multiply($this->quantity);
    }
}
