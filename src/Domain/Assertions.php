<?php

declare(strict_types=1);

namespace Mika\Domain;

use InvalidArgumentException;

trait Assertions
{
    protected function assertArgumentNotEmpty($argument, string $message = ''): void
    {
        if (empty($argument)) {
            throw new InvalidArgumentException($message);
        }
    }

    protected function assertArgumentMaxLength($argument, int $length, string $message = ''): void
    {
        if (mb_strlen($argument) > $length) {
            throw new InvalidArgumentException($message);
        }
    }
}
