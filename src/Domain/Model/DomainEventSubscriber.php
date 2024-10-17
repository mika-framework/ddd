<?php

declare(strict_types=1);

namespace Mika\Domain\Model;

interface DomainEventSubscriber
{
    public function handle(DomainEvent $domainEvent);
}
