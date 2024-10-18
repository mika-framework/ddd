<?php

declare(strict_types=1);

namespace Mika\Domain\Model;

abstract class Identity extends ValueObject
{
    protected string $id;

    public function __construct(string $id)
    {
        $this->setId($id);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function equals(Identity $other): bool
    {
        if (get_class($this) == get_class($other)) {
            return $this->id() == $other->id();
        }

        return false;
    }

    protected function setId(string $id): void
    {
        $this->assertNotEmpty($id, 'Identifier cannot be empty');
        $this->validateId($id);

        $this->id = $id;
    }

    protected function validateId(string $id)
    {
        // You can override this method when custom validation is required
    }
}
