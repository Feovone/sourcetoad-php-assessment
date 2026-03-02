<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Sourcetoad\Assessment\Question3\Contract\StubShippingProvider;
use Sourcetoad\Assessment\Question3\Domain\Address;
use Sourcetoad\Assessment\Question3\Domain\Cart;
use Sourcetoad\Assessment\Question3\Domain\Customer;
use Sourcetoad\Assessment\Question3\Domain\Item;
use Sourcetoad\Assessment\Question3\Domain\Money;

$shippingProvider = new StubShippingProvider(Money::fromDollars(5.99));
$cart = new Cart($shippingProvider);

$customer = new Customer('John', 'Doe');
$customer->addAddress(new Address('Main St', 'Apt 4', 'Tampa', 'FL', '33601'));
$customer->addAddress(new Address('Oak Ave', 'Building 12', 'Orlando', 'FL', '32801'));
$cart->setCustomer($customer);

$cart->setShippingAddress($customer->getAddresses()[0]);

$cart->addItem(new Item(1, 'Wireless Mouse', Money::fromDollars(29.99)), 2);
$cart->addItem(new Item(2, 'Mechanical Keyboard', Money::fromDollars(89.99)));
$cart->addItem(new Item(3, 'USB-C Hub', Money::fromDollars(19.99)), 3);

$cart->printSummary();
