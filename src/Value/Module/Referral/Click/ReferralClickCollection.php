<?php

namespace Nebalus\Webapi\Value\Module\Referral\Click;

use IteratorAggregate;
use Traversable;

class ReferralClickCollection implements IteratorAggregate
{
    private array $referralClicks;

    private function __construct(ReferralClick ...$referralClicks)
    {
        $this->referralClicks = $referralClicks;
    }

    public static function fromObjects(ReferralClick ...$referralClicks): self
    {
        return new self(...$referralClicks);
    }

    public function getIterator(): Traversable
    {
        yield from $this->referralClicks;
    }
}
