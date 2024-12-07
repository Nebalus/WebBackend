<?php

use Nebalus\Webapi\Value\Referral\Click\ReferralClick;

class ReferralClicks implements IteratorAggregate
{
    private array $referralClicks;

    private function __construct(ReferralClick ...$referralClicks)
    {
        $this->referralClicks = $referralClicks;
    }

    public static function fromArray(ReferralClick ...$referralClicks): self
    {
        return new self(...$referralClicks);
    }

    public function getIterator(): Traversable
    {
        yield from $this->referralClicks;
    }
}
