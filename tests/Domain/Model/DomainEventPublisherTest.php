<?php

declare(strict_types=1);

namespace Mika\Tests\Domain\Model;

use Mika\Domain\Model\DomainEventPublisher;
use Mika\Tests\Container;
use PHPUnit\Framework\TestCase;

// Domain events
class TestDomainEventOne extends TestDomainEvent {}
class TestDomainEventTwo extends TestDomainEvent {}
class TestDomainEventThree extends TestDomainEvent {}

// Domain event subscribers
class TestDomainEventSubscriberOne extends TestDomainEventSubscriber {}
class TestDomainEventSubscriberTwo extends TestDomainEventSubscriber {}
class TestDomainEventSubscriberOneTwo extends TestDomainEventSubscriber {}
class TestDomainEventSubscriberAll extends TestDomainEventSubscriber {}

class DomainEventPublisherTest extends TestCase
{
    protected Container $container;

    protected TestDomainEventSubscriberOne $domainEventSubscriberOne;
    protected TestDomainEventSubscriberTwo $domainEventSubscriberTwo;
    protected TestDomainEventSubscriberOneTwo $domainEventSubscriberOneTwo;
    protected TestDomainEventSubscriberAll $domainEventSubscriberAll;

    protected function setUp(): void
    {
        parent::setUp();

        $this->domainEventSubscriberOne = new TestDomainEventSubscriberOne();
        $this->domainEventSubscriberTwo = new TestDomainEventSubscriberTwo();
        $this->domainEventSubscriberOneTwo = new TestDomainEventSubscriberOneTwo();
        $this->domainEventSubscriberAll = new TestDomainEventSubscriberAll();

        $this->container = new Container();
        $this->container->services[TestDomainEventSubscriberOne::class] = $this->domainEventSubscriberOne;
        $this->container->services[TestDomainEventSubscriberTwo::class] = $this->domainEventSubscriberTwo;
        $this->container->services[TestDomainEventSubscriberOneTwo::class] = $this->domainEventSubscriberOneTwo;
        $this->container->services[TestDomainEventSubscriberAll::class] = $this->domainEventSubscriberAll;

        $domainEventPublisher = DomainEventPublisher::instance();

        $domainEventPublisher->subscribe(TestDomainEventSubscriberOne::class, TestDomainEventOne::class);
        $domainEventPublisher->subscribe(TestDomainEventSubscriberTwo::class, TestDomainEventTwo::class);
        $domainEventPublisher->subscribe(TestDomainEventSubscriberOneTwo::class, [TestDomainEventOne::class, TestDomainEventTwo::class]);
        $domainEventPublisher->subscribe(TestDomainEventSubscriberAll::class, '*');
    }

    public function testPublisherFailsWhenContainerNotSet(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();

        $domainEventOne = new TestDomainEventOne();

        $domainEventPublisher->subscribe(TestDomainEventSubscriberOne::class, TestDomainEventOne::class);

        $this->expectException(\Exception::class);
        $domainEventPublisher->publish($domainEventOne, DomainEventPublisher::MODE_PROCESS);
    }

    public function testPublishedEventsSentToSubscribers(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventPublisher->setContainer($this->container);

        $domainEventPublisher->publish(new TestDomainEventOne(), DomainEventPublisher::MODE_PROCESS);

        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([], $this->domainEventSubscriberTwo->handledEvents());
        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberOneTwo->handledEvents());
        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberAll->handledEvents());

        $domainEventPublisher->publish(new TestDomainEventTwo(), DomainEventPublisher::MODE_PROCESS);
        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([TestDomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());
        $this->assertEquals([TestDomainEventOne::class, TestDomainEventTwo::class], $this->domainEventSubscriberOneTwo->handledEvents());
        $this->assertEquals([TestDomainEventOne::class, TestDomainEventTwo::class], $this->domainEventSubscriberAll->handledEvents());

        $domainEventPublisher->publish(new TestDomainEventThree(), DomainEventPublisher::MODE_PROCESS);
        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([TestDomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());
        $this->assertEquals([TestDomainEventOne::class, TestDomainEventTwo::class], $this->domainEventSubscriberOneTwo->handledEvents());
        $this->assertEquals([TestDomainEventOne::class, TestDomainEventTwo::class, TestDomainEventThree::class], $this->domainEventSubscriberAll->handledEvents());
    }

    public function testDelayedModeWaitsForProcessing(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventPublisher->setContainer($this->container);

        $domainEventPublisher->publish(new TestDomainEventOne());
        $this->assertEquals([], $this->domainEventSubscriberOne->handledEvents());

        $domainEventPublisher->process();
        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
    }

    public function testProcessModeOnlyProcessesOneEvent(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventPublisher->setContainer($this->container);

        $domainEventPublisher->publish(new TestDomainEventOne());
        $this->assertEquals([], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([], $this->domainEventSubscriberTwo->handledEvents());

        $domainEventPublisher->publish(new TestDomainEventTwo(), DomainEventPublisher::MODE_PROCESS);
        $this->assertEquals([], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([TestDomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());

        $domainEventPublisher->process();
        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([TestDomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());
    }

    public function testProcessAllModeProcessesAllEvents(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventPublisher->setContainer($this->container);

        $domainEventPublisher->publish(new TestDomainEventOne());
        $this->assertEquals([], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([], $this->domainEventSubscriberTwo->handledEvents());

        $domainEventPublisher->publish(new TestDomainEventTwo(), DomainEventPublisher::MODE_PROCESS_ALL);
        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([TestDomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());

        $domainEventPublisher->process();
        $this->assertEquals([TestDomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([TestDomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());
    }
}
