<?php

declare(strict_types=1);

namespace Mika\Domain\Model;

interface DomainEvent
{
    public function occurredAt(): \DateTimeImmutable;
}
