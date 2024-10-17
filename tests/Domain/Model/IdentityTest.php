<?php

declare(strict_types=1);

namespace Mika\Tests\Domain\Model;

use Mika\Domain\Model\Identity;
use PHPUnit\Framework\TestCase;

class MyIdentityOne extends Identity
{
    //
}

class MyIdentityTwo extends Identity
{
    protected function validateId(string $id): void
    {
        $this->assertArgumentMaxLength($id, 16, 'Identifier cannot be longer than 16 characters');
    }
}

class IdentityTest extends TestCase
{
    public function testIdentityIsCreated(): void
    {
        $identityOne = new MyIdentityOne('my_one');
        $this->assertEquals('my_one', $identityOne->id());
    }

    public function testIdentityCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier cannot be empty');
        new MyIdentityOne('');
    }

    public function testIdentityCustomValidation(): void
    {
        $identityTwo = new MyIdentityTwo('my_two');
        $this->assertEquals('my_two', $identityTwo->id());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier cannot be longer than 16 characters');
        new MyIdentityTwo('my_01234567890123456789');
    }

    public function testIdentityEqualsOtherIdentity(): void
    {
        $identityOne = new MyIdentityOne('my_identity');

        $equalIdentityOne = new MyIdentityOne('my_identity');

        $notEqualIdentityOne = new MyIdentityOne('other_identity');
        $notEqualIdentityTwo1 = new MyIdentityTwo('my_identity');
        $notEqualIdentityTwo2 = new MyIdentityTwo('other_identity');

        $this->assertTrue($identityOne->equals($equalIdentityOne));

        $this->assertFalse($identityOne->equals($notEqualIdentityOne));
        $this->assertFalse($identityOne->equals($notEqualIdentityTwo1));
        $this->assertFalse($identityOne->equals($notEqualIdentityTwo2));
    }
}
