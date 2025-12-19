<?php

namespace Nebalus\Webapi\Value\Account\InvitationToken;

use IteratorAggregate;
use Traversable;

class InvitationTokenCollection implements IteratorAggregate
{
    private array $invitationsTokens;

    private function __construct(InvitationToken ...$invitationsTokens)
    {
        $this->invitationsTokens = $invitationsTokens;
    }

    public static function fromObjects(InvitationToken ...$invitationsTokens): self
    {
        return new self(...$invitationsTokens);
    }

    public function getIterator(): Traversable
    {
        yield from $this->invitationsTokens;
    }
}
