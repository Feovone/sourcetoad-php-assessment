<?php

declare(strict_types=1);

namespace Sourcetoad\Assessment\Question3\Contract;

use Sourcetoad\Assessment\Question3\Domain\Address;
use Sourcetoad\Assessment\Question3\Domain\Item;
use Sourcetoad\Assessment\Question3\Domain\Money;

interface ShippingRateProvider
{
    public function getRate(Item $item, Address $destination): Money;
}
