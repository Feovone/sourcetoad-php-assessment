<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question3\Domain;

use InvalidArgumentException;

final class Address
{
    public function __construct(
        private readonly string $street,
        private readonly string $building,
        private readonly string $city,
        private readonly string $state,
        private readonly string $zip,
    ) {
        if (trim($street) === '') {
            throw new InvalidArgumentException('Street is required.');
        }
        if (trim($city) === '') {
            throw new InvalidArgumentException('City is required.');
        }
        if (trim($state) === '') {
            throw new InvalidArgumentException('State is required.');
        }
        if (trim($zip) === '') {
            throw new InvalidArgumentException('Zip is required.');
        }
    }

    public function street(): string { return $this->street; }
    public function building(): string { return $this->building; }
    public function city(): string { return $this->city; }
    public function state(): string { return $this->state; }
    public function zip(): string { return $this->zip; }

    public function __toString(): string
    {
        $parts = array_filter([$this->street, $this->building, $this->city, $this->state, $this->zip]);
        return implode(', ', $parts);
    }
}
