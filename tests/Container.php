<?php

declare(strict_types=1);

namespace Mika\Tests;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    public array $services = [];

    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \Exception('Service not found');
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
