<?php

declare(strict_types=1);

namespace Mika\Domain\Model;

interface DomainEventSubscriber
{
    /**
     * @param DomainEvent $domainEvent
     * @return void
     */
    public function handle(DomainEvent $domainEvent): void;
}
