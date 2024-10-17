<?php

declare(strict_types=1);

namespace Mika\Tests\Domain\Model;

use Mika\Domain\Model\DomainEvent;

class TestDomainEvent implements DomainEvent
{
    private \DateTimeImmutable $occurredAt;

    public function __construct() {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
