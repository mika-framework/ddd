<?php

declare(strict_types=1);

namespace Mika\Tests;

use Mika\AssertionConcern;
use Mika\Domain\Model\AbstractId;
use PHPUnit\Framework\TestCase;

class AssertionConcernObject
{
    use AssertionConcern;

    public function invokeNotEmpty($argument, string $message = ''): void
    {
        $this->assertNotEmpty($argument, $message);
    }

    public function invokeNotNull($argument, string $message = ''): void
    {
        $this->assertNotNull($argument, $message);
    }

    public function invokeEquals($argument1, $argument2, string $message = ''): void
    {
        $this->assertEquals($argument1, $argument2, $message);
    }

    public function invokeNotEquals($argument1, $argument2, string $message = ''): void
    {
        $this->assertNotEquals($argument1, $argument2, $message);
    }

    public function invokeTrue($argument, string $message = ''): void
    {
        $this->assertTrue($argument, $message);
    }

    public function invokeFalse($argument, string $message = ''): void
    {
        $this->assertFalse($argument, $message);
    }

    public function invokeMinLength($argument, int $length, string $message = ''): void
    {
        $this->assertMinLength($argument, $length, $message);
    }

    public function invokeMaxLength($argument, int $length, string $message = ''): void
    {
        $this->assertMaxLength($argument, $length, $message);
    }

    public function invokeLength($argument, int $minLength, int $maxLength, string $message = ''): void
    {
        $this->assertLength($argument, $minLength, $maxLength, $message);
    }

    public function invokeRange($argument, $min, $max, string $message = ''): void
    {
        $this->assertRange($argument, $min, $max, $message);
    }
}

class IdObject extends AbstractId {}

class AssertionConcernTest extends TestCase
{
    protected AssertionConcernObject $object;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new AssertionConcernObject();
    }

    public function testNotEmptyAssertion(): void
    {
        $this->object->invokeNotEmpty('something');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeNotEmpty('', 'Exception message');
    }

    public function testNotNullAssertion(): void
    {
        $this->object->invokeNotNull('something');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeNotNull(null, 'Exception message');
    }

    public function testEqualsAssertionWithValues(): void
    {
        $this->object->invokeEquals(1, 1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeEquals(1, 2, 'Exception message');
    }

    public function testEqualsAssertionWithObjects(): void
    {
        $object = new IdObject('1');
        $equalObject = new IdObject('1');
        $notEqualObject = new IdObject('2');

        $this->object->invokeEquals($object, $equalObject);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeEquals($object, $notEqualObject, 'Exception message');
    }

    public function testNotEqualsAssertionWithValues(): void
    {
        $this->object->invokeNotEquals(1, 2);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeNotEquals(1, 1, 'Exception message');
    }

    public function testNotEqualsAssertionWithObjects(): void
    {
        $object = new IdObject('1');
        $equalObject = new IdObject('1');
        $notEqualObject = new IdObject('2');

        $this->object->invokeNotEquals($object, $notEqualObject);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeNotEquals($object, $equalObject, 'Exception message');
    }

    public function testTrueAssertion(): void
    {
        $this->object->invokeTrue(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeTrue(false, 'Exception message');
    }

    public function testFalseAssertion(): void
    {
        $this->object->invokeFalse(false);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeFalse(true, 'Exception message');
    }

    public function testMinLengthAssertion(): void
    {
        $this->object->invokeMinLength('string', 6);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeMinLength('string', 7, 'Exception message');
    }

    public function testMaxLengthAssertion(): void
    {
        $this->object->invokeMaxLength('string', 6);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeMaxLength('string', 5, 'Exception message');
    }

    public function testLengthAssertionMin(): void
    {
        $this->object->invokeLength('ab', 2, 2);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeLength('ab', 1, 1, 'Exception message');
    }

    public function testLengthAssertionMax(): void
    {
        $this->object->invokeLength('ab', 2, 2);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeLength('ab', 3, 3, 'Exception message');
    }

    public function testRangeAssertionMin(): void
    {
        $this->object->invokeRange(100, 100, 100);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeRange(100, 99, 99, 'Exception message');
    }

    public function testRangeAssertionMax(): void
    {
        $this->object->invokeRange(100, 100, 100);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exception message');

        $this->object->invokeRange(100, 101, 101, 'Exception message');
    }
}
