<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question3\Domain;

use InvalidArgumentException;

final class Money
{
    public function __construct(
        private readonly int $cents,
    ) {
        if ($cents < 0) {
            throw new InvalidArgumentException('Money cannot be negative.');
        }
    }

    public static function fromDollars(float $amount): self
    {
        return new self((int) round($amount * 100));
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function multiply(int $factor): self
    {
        return new self($this->cents * $factor);
    }

    public function percentage(float $rate): self
    {
        return new self((int) round($this->cents * $rate));
    }

    public function format(): string
    {
        return '$' . number_format($this->cents / 100, 2);
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
