<?php

namespace Nebalus\Webapi\Value\Module\Referral;

use IteratorAggregate;
use Traversable;

class ReferralCollection implements IteratorAggregate
{
    private array $referrals;

    private function __construct(Referral ...$referrals)
    {
        $this->referrals = $referrals;
    }

    public static function fromObjects(Referral ...$referrals): self
    {
        return new self(...$referrals);
    }

    public function getIterator(): Traversable
    {
        yield from $this->referrals;
    }
}
