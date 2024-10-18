<?php

declare(strict_types=1);

namespace Mika\Domain\Model\Event;

use Mika\Domain\Model\DomainEvent;
use Mika\Domain\Model\DomainEventSubscriber;

class EventStoreSubscriber implements DomainEventSubscriber
{
    /**
     * @param EventStore $eventStore
     */
    public function __construct(
        protected EventStore $eventStore,
    ) {}

    /**
     * @param DomainEvent $domainEvent
     * @return void
     */
    public function handle(DomainEvent $domainEvent): void
    {
        $this->eventStore->append($domainEvent);
    }
}
