<?php

declare(strict_types=1);

namespace Mika\Tests\Domain\Model;

use Mika\Domain\Model\DomainEventPublisher;
use Mika\Tests\Container;
use PHPUnit\Framework\TestCase;

// Domain events
class DomainEventOne extends DomainEventMock {}
class DomainEventTwo extends DomainEventMock {}
class DomainEventThree extends DomainEventMock {}

// Domain event subscribers
class DomainEventSubscriberOne extends DomainEventSubscriberMock {}
class DomainEventSubscriberTwo extends DomainEventSubscriberMock {}
class DomainEventSubscriberOneTwo extends DomainEventSubscriberMock {}
class DomainEventSubscriberAll extends DomainEventSubscriberMock {}

class DomainEventPublisherTest extends TestCase
{
    protected Container $container;

    protected DomainEventSubscriberOne $domainEventSubscriberOne;
    protected DomainEventSubscriberTwo $domainEventSubscriberTwo;
    protected DomainEventSubscriberOneTwo $domainEventSubscriberOneTwo;
    protected DomainEventSubscriberAll $domainEventSubscriberAll;

    protected function setUp(): void
    {
        parent::setUp();

        $this->domainEventSubscriberOne = new DomainEventSubscriberOne();
        $this->domainEventSubscriberTwo = new DomainEventSubscriberTwo();
        $this->domainEventSubscriberOneTwo = new DomainEventSubscriberOneTwo();
        $this->domainEventSubscriberAll = new DomainEventSubscriberAll();

        $this->container = new Container();
        $this->container->services[DomainEventSubscriberOne::class] = $this->domainEventSubscriberOne;
        $this->container->services[DomainEventSubscriberTwo::class] = $this->domainEventSubscriberTwo;
        $this->container->services[DomainEventSubscriberOneTwo::class] = $this->domainEventSubscriberOneTwo;
        $this->container->services[DomainEventSubscriberAll::class] = $this->domainEventSubscriberAll;

        $domainEventPublisher = DomainEventPublisher::instance();

        $domainEventPublisher->subscribe(DomainEventSubscriberOne::class, DomainEventOne::class);
        $domainEventPublisher->subscribe(DomainEventSubscriberTwo::class, DomainEventTwo::class);
        $domainEventPublisher->subscribe(DomainEventSubscriberOneTwo::class, [DomainEventOne::class, DomainEventTwo::class]);
        $domainEventPublisher->subscribe(DomainEventSubscriberAll::class, '*');
    }

    public function testPublisherFailsWhenContainerNotSet(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();

        $domainEventOne = new DomainEventOne();

        $domainEventPublisher->subscribe(DomainEventSubscriberOne::class, DomainEventOne::class);

        $this->expectException(\Exception::class);
        $domainEventPublisher->publish($domainEventOne, DomainEventPublisher::MODE_PROCESS);
    }

    public function testPublishedEventsSentToSubscribers(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventPublisher->setContainer($this->container);

        $domainEventPublisher->publish(new DomainEventOne(), DomainEventPublisher::MODE_PROCESS);

        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([], $this->domainEventSubscriberTwo->handledEvents());
        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberOneTwo->handledEvents());
        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberAll->handledEvents());

        $domainEventPublisher->publish(new DomainEventTwo(), DomainEventPublisher::MODE_PROCESS);
        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([DomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());
        $this->assertEquals([DomainEventOne::class, DomainEventTwo::class], $this->domainEventSubscriberOneTwo->handledEvents());
        $this->assertEquals([DomainEventOne::class, DomainEventTwo::class], $this->domainEventSubscriberAll->handledEvents());

        $domainEventPublisher->publish(new DomainEventThree(), DomainEventPublisher::MODE_PROCESS);
        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([DomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());
        $this->assertEquals([DomainEventOne::class, DomainEventTwo::class], $this->domainEventSubscriberOneTwo->handledEvents());
        $this->assertEquals([DomainEventOne::class, DomainEventTwo::class, DomainEventThree::class], $this->domainEventSubscriberAll->handledEvents());
    }

    public function testDelayedModeWaitsForProcessing(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventPublisher->setContainer($this->container);

        $domainEventPublisher->publish(new DomainEventOne());
        $this->assertEquals([], $this->domainEventSubscriberOne->handledEvents());

        $domainEventPublisher->process();
        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
    }

    public function testProcessModeOnlyProcessesOneEvent(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventPublisher->setContainer($this->container);

        $domainEventPublisher->publish(new DomainEventOne());
        $this->assertEquals([], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([], $this->domainEventSubscriberTwo->handledEvents());

        $domainEventPublisher->publish(new DomainEventTwo(), DomainEventPublisher::MODE_PROCESS);
        $this->assertEquals([], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([DomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());

        $domainEventPublisher->process();
        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([DomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());
    }

    public function testProcessAllModeProcessesAllEvents(): void
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventPublisher->setContainer($this->container);

        $domainEventPublisher->publish(new DomainEventOne());
        $this->assertEquals([], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([], $this->domainEventSubscriberTwo->handledEvents());

        $domainEventPublisher->publish(new DomainEventTwo(), DomainEventPublisher::MODE_PROCESS_ALL);
        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([DomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());

        $domainEventPublisher->process();
        $this->assertEquals([DomainEventOne::class], $this->domainEventSubscriberOne->handledEvents());
        $this->assertEquals([DomainEventTwo::class], $this->domainEventSubscriberTwo->handledEvents());
    }
}
