<?php

interface ShippingRateProvider
{
    public function getRate(Item $item, Address $destination): float;
}

class StubShippingProvider implements ShippingRateProvider
{
    public function __construct(
        private readonly float $flatRate = 5.99,
    ) {}

    public function getRate(Item $item, Address $destination): float
    {
        return $this->flatRate;
    }
}

class Address
{
    public function __construct(
        public readonly string $street,
        public readonly string $building,
        public readonly string $city,
        public readonly string $state,
        public readonly string $zip,
    ) {}

    public function __toString(): string
    {
        $parts = array_filter([$this->street, $this->building, $this->city, $this->state, $this->zip]);
        return implode(', ', $parts);
    }
}

class Item
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly int    $quantity,
        public readonly float  $price,
    ) {}

    public function lineTotal(): float
    {
        return $this->price * $this->quantity;
    }
}

class Customer
{
    private array $addresses = [];

    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
    ) {}

    public function fullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function addAddress(Address $address): void
    {
        $this->addresses[] = $address;
    }

    public function getAddresses(): array
    {
        return $this->addresses;
    }
}

class Cart
{
    private const float TAX_RATE = 0.07;

    private ?Customer $customer = null;
    private ?Address  $shippingAddress = null;
    private array $items = [];

    public function __construct(
        private readonly ShippingRateProvider $shippingProvider,
    ) {}

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setShippingAddress(Address $address): void
    {
        $this->shippingAddress = $address;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function itemCostBreakdown(Item $item): array
    {
        $lineTotal = $item->lineTotal();
        $tax       = $lineTotal * self::TAX_RATE;
        $shipping  = $this->shippingAddress
            ? $this->shippingProvider->getRate($item, $this->shippingAddress) * $item->quantity
            : 0;

        return [
            'line_total' => $lineTotal,
            'tax'        => $tax,
            'shipping'   => $shipping,
            'total'      => $lineTotal + $tax + $shipping,
        ];
    }

    public function subtotal(): float
    {
        return array_reduce(
            $this->items,
            fn(float $carry, Item $item) => $carry + $item->lineTotal(),
            0.00,
        );
    }

    public function total(): float
    {
        $subtotal = $this->subtotal();
        $tax      = $subtotal * self::TAX_RATE;
        $shipping = 0;

        if ($this->shippingAddress) {
            foreach ($this->items as $item) {
                $shipping += $this->shippingProvider->getRate($item, $this->shippingAddress) * $item->quantity;
            }
        }

        return $subtotal + $tax + $shipping;
    }

    public function printSummary(): void
    {
        if ($this->customer) {
            echo "Customer: {$this->customer->fullName()}\n";
            foreach ($this->customer->getAddresses() as $i => $addr) {
                echo "  Address " . ($i + 1) . ": {$addr}\n";
            }
        }

        if ($this->shippingAddress) {
            echo "Ships to: {$this->shippingAddress}\n";
        }

        echo "\nItems:\n";
        foreach ($this->items as $item) {
            $bd = $this->itemCostBreakdown($item);
            echo "  {$item->name} (x{$item->quantity}) - Price: {$bd['line_total']}, Tax: {$bd['tax']}, Ship: {$bd['shipping']}, Total: {$bd['total']}\n";
        }

        echo "\nSubtotal: {$this->subtotal()}\n";
        echo "Total: {$this->total()}\n";
    }
}

$shippingProvider = new StubShippingProvider(5.99);
$cart = new Cart($shippingProvider);

$customer = new Customer('John', 'Doe');
$customer->addAddress(new Address('Main St', 'Apt 4', 'Tampa', 'FL', '33601'));
$customer->addAddress(new Address('Oak Ave', 'Building 12', 'Orlando', 'FL', '32801'));
$cart->setCustomer($customer);

$cart->setShippingAddress($customer->getAddresses()[0]);

$cart->addItem(new Item(1, 'Wireless Mouse', 2, 29.99));
$cart->addItem(new Item(2, 'Mechanical Keyboard', 1, 89.99));
$cart->addItem(new Item(3, 'USB-C Hub', 3, 19.99));

$cart->printSummary();
