<?php

declare(strict_types=1);

namespace Mika\Domain\Model\Event;

use Mika\Domain\Model\DomainEvent;
use Mika\Domain\Model\Entity;

class StoredEvent extends Entity
{
    /**
     * @param DomainEvent $domainEvent
     * @param int|null $storedEventId
     */
    public function __construct(
        protected readonly DomainEvent $domainEvent,
        protected readonly ?int $storedEventId = null,
    ) {}

    /**
     * @return int|null
     */
    public function storedEventId(): ?int
    {
        return $this->storedEventId;
    }

    /**
     * @return DomainEvent
     */
    public function domainEvent(): DomainEvent
    {
        return $this->domainEvent;
    }
}
