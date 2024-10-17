<?php

declare(strict_types=1);

namespace Mika\Tests\Domain\Model;

use Mika\Domain\Model\DomainEvent;
use Mika\Domain\Model\DomainEventSubscriber;

class DomainEventSubscriberMock implements DomainEventSubscriber
{
    private array $handledEvents = [];

    public function handle(DomainEvent $domainEvent): void
    {
        $this->handledEvents[] = get_class($domainEvent);
    }

    public function handledEvents(): array
    {
        return $this->handledEvents;
    }
}
