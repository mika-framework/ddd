<?php

declare(strict_types=1);

namespace Mika\Tests\Domain\Model;

use Mika\Domain\Model\AbstractId;
use PHPUnit\Framework\TestCase;

class MyIdOne extends AbstractId
{
    //
}

class MyIdTwo extends AbstractId
{
    protected function validateId(string $id): void
    {
        $this->assertMaxLength($id, 16, 'Identifier cannot be longer than 16 characters');
    }
}

class AbstractIdTest extends TestCase
{
    public function testIdentityIsCreated(): void
    {
        $identityOne = new MyIdOne('my_one');
        $this->assertEquals('my_one', $identityOne->id());
    }

    public function testIdentityCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier cannot be empty');
        new MyIdOne('');
    }

    public function testIdentityCustomValidation(): void
    {
        $identityTwo = new MyIdTwo('my_two');
        $this->assertEquals('my_two', $identityTwo->id());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier cannot be longer than 16 characters');
        new MyIdTwo('my_01234567890123456789');
    }

    public function testIdentityEqualsOtherIdentity(): void
    {
        $identityOne = new MyIdOne('my_identity');

        $equalIdentityOne = new MyIdOne('my_identity');

        $notEqualIdentityOne = new MyIdOne('other_identity');
        $notEqualIdentityTwo1 = new MyIdTwo('my_identity');
        $notEqualIdentityTwo2 = new MyIdTwo('other_identity');

        $this->assertTrue($identityOne->equals($equalIdentityOne));

        $this->assertFalse($identityOne->equals($notEqualIdentityOne));
        $this->assertFalse($identityOne->equals($notEqualIdentityTwo1));
        $this->assertFalse($identityOne->equals($notEqualIdentityTwo2));
    }
}
