<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question3\Domain;

use LogicException;
use Sourcetoad\Assessment\Question3\Contract\ShippingRateProvider;

final class Cart
{
    private const TAX_RATE = 0.07;

    private ?Customer $customer = null;
    private ?Address  $shippingAddress = null;

    /** @var array<int, CartLine> keyed by Item id */
    private array $lines = [];

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

    public function setShippingAddress(Address $address): void
    {
        $this->shippingAddress = $address;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function addItem(Item $item, int $quantity = 1): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $id = $item->id();

        if (isset($this->lines[$id])) {
            $this->lines[$id]->increaseQuantity($quantity);
        } else {
            $this->lines[$id] = new CartLine($item, $quantity);
        }
    }

    public function removeItem(int $itemId): void
    {
        if (!isset($this->lines[$itemId])) {
            throw new \InvalidArgumentException("Item {$itemId} is not in the cart.");
        }
        unset($this->lines[$itemId]);
    }

    /** @return CartLine[] */
    public function getLines(): array
    {
        return array_values($this->lines);
    }

    public function isEmpty(): bool
    {
        return empty($this->lines);
    }

    public function lineCostBreakdown(CartLine $line): array
    {
        $lineTotal = $line->lineTotal();
        $tax       = $lineTotal->percentage(self::TAX_RATE);
        $shipping  = $this->shippingAddress
            ? $this->shippingProvider->getRate($line->item(), $this->shippingAddress)->multiply($line->quantity())
            : Money::zero();

        return [
            'line_total' => $lineTotal,
            'tax'        => $tax,
            'shipping'   => $shipping,
            'total'      => $lineTotal->add($tax)->add($shipping),
        ];
    }

    public function subtotal(): Money
    {
        $this->guardNotEmpty();

        return array_reduce(
            $this->getLines(),
            fn(Money $carry, CartLine $line) => $carry->add($line->lineTotal()),
            Money::zero(),
        );
    }

    public function totalTax(): Money
    {
        return $this->subtotal()->percentage(self::TAX_RATE);
    }

    public function totalShipping(): Money
    {
        if (!$this->shippingAddress) {
            return Money::zero();
        }

        $shipping = Money::zero();

        foreach ($this->lines as $line) {
            $rate = $this->shippingProvider->getRate($line->item(), $this->shippingAddress);
            $shipping = $shipping->add($rate->multiply($line->quantity()));
        }

        return $shipping;
    }

    public function total(): Money
    {
        return $this->subtotal()
            ->add($this->totalTax())
            ->add($this->totalShipping());
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
        foreach ($this->getLines() as $line) {
            $bd = $this->lineCostBreakdown($line);
            echo "  {$line->item()->name()} (x{$line->quantity()})"
               . " - Price: {$bd['line_total']}"
               . ", Tax: {$bd['tax']}"
               . ", Ship: {$bd['shipping']}"
               . ", Total: {$bd['total']}\n";
        }

        echo "\nSubtotal: {$this->subtotal()}\n";
        echo "Tax:      {$this->totalTax()}\n";
        echo "Shipping: {$this->totalShipping()}\n";
        echo "Total:    {$this->total()}\n";
    }

    private function guardNotEmpty(): void
    {
        if ($this->isEmpty()) {
            throw new LogicException('Cart is empty.');
        }
    }
}
