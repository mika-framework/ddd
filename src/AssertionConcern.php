<?php

declare(strict_types=1);

namespace Mika;

use InvalidArgumentException;

trait AssertionConcern
{
    protected function assertNotEmpty($argument, string $message = ''): void
    {
        if (empty($argument)) {
            throw new InvalidArgumentException($message ?: 'Argument cannot be empty');
        }
    }

    protected function assertNotNull($argument, string $message = ''): void
    {
        if (is_null($argument)) {
            throw new InvalidArgumentException($message ?: 'Argument cannot be null');
        }
    }

    protected function assertEquals($argument1, $argument2, string $message = ''): void
    {
        if (is_object($argument1) && is_object($argument2)) {
            if (!$argument1->equals($argument2)) {
                throw new InvalidArgumentException($message ?: 'Arguments must be equal');
            }
        } else if ($argument1 != $argument2) {
            throw new InvalidArgumentException($message ?: 'Arguments must be equal');
        }
    }

    protected function assertNotEquals($argument1, $argument2, string $message = ''): void
    {
        if (is_object($argument1) && is_object($argument2)) {
            if ($argument1->equals($argument2)) {
                throw new InvalidArgumentException($message ?: 'Arguments must not be equal');
            }
        } else if ($argument1 == $argument2) {
            throw new InvalidArgumentException($message ?: 'Arguments must not be equal');
        }
    }

    protected function assertTrue($argument, string $message = ''): void
    {
        if (!$argument) {
            throw new InvalidArgumentException($message ?: 'Argument cannot be false');
        }
    }

    protected function assertFalse($argument, string $message = ''): void
    {
        if ($argument) {
            throw new InvalidArgumentException($message ?: 'Argument cannot be true');
        }
    }

    protected function assertMinLength($argument, int $length, string $message = ''): void
    {
        if (mb_strlen($argument) < $length) {
            throw new InvalidArgumentException($message ?: 'Argument length must be greater than or equal to ' . $length);
        }
    }

    protected function assertMaxLength($argument, int $length, string $message = ''): void
    {
        if (mb_strlen($argument) > $length) {
            throw new InvalidArgumentException($message ?: 'Argument length must be less than or equal to ' . $length);
        }
    }

    protected function assertLength($argument, int $minLength, int $maxLength, string $message = ''): void
    {
        if (mb_strlen($argument) < $minLength || mb_strlen($argument) > $maxLength) {
            throw new InvalidArgumentException($message ?: 'Argument length must be between ' . $minLength . ' and ' . $maxLength);
        }
    }

    protected function assertRange($argument, $min, $max, string $message = ''): void
    {
        if ($argument < $min || $argument > $max) {
            throw new InvalidArgumentException($message ?: 'Argument must be between ' . $min . ' and ' . $max);
        }
    }
}
