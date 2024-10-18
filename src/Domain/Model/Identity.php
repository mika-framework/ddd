<?php

declare(strict_types=1);

namespace Mika\Domain\Model;

interface Identity
{
    public function id(): string;
}
