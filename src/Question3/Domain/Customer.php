<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question3\Domain;

use InvalidArgumentException;

final class Customer
{
    /** @var Address[] */
    private array $addresses = [];

    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
    ) {
        if (trim($firstName) === '') {
            throw new InvalidArgumentException('First name cannot be empty.');
        }
        if (trim($lastName) === '') {
            throw new InvalidArgumentException('Last name cannot be empty.');
        }
    }

    public function firstName(): string { return $this->firstName; }
    public function lastName(): string { return $this->lastName; }

    public function fullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function addAddress(Address $address): void
    {
        $this->addresses[] = $address;
    }

    public function removeAddress(int $index): void
    {
        if (!isset($this->addresses[$index])) {
            throw new InvalidArgumentException("No address at index {$index}.");
        }
        array_splice($this->addresses, $index, 1);
    }

    /** @return Address[] */
    public function getAddresses(): array
    {
        return $this->addresses;
    }
}
