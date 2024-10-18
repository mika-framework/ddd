<?php

declare(strict_types=1);

namespace Mika\Domain\Model\Event;

use Mika\Domain\Model\DomainEvent;

interface EventStore
{
    /**
     * @param DomainEvent $domainEvent
     * @return void
     */
    public function append(DomainEvent $domainEvent): void;

    /**
     * @param int $storedEventId
     * @return StoredEvent[]
     */
    public function allStoredEventsSince(int $storedEventId): array;
}
