<?php

declare(strict_types=1);

namespace Mika\Domain\Model;

use Psr\Container\ContainerInterface;

class DomainEventPublisher
{
    public const MODE_DELAY = 0;
    public const MODE_PROCESS = 1;
    public const MODE_PROCESS_ALL = 2;

    private ?ContainerInterface $container;

    private array $subscribers = [];

    /**
     * @var DomainEvent[]
     */
    private array $publishedEvents = [];

    public static function instance(): DomainEventPublisher
    {
        static $instance;

        if (is_null($instance)) {
            $instance = new DomainEventPublisher();
        }

        return $instance;
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function subscribe(string $subscriberClass, string|array $eventClass): void
    {
        $eventClasses = is_array($eventClass) ? $eventClass : [$eventClass];

        foreach ($eventClasses as $eventClass) {
            if (!in_array($eventClass, $this->subscribers[$subscriberClass] ?? [])) {
                $this->subscribers[$subscriberClass][] = $eventClass;
            }
        }
    }

    public function publish(DomainEvent $event, int $mode = self::MODE_DELAY): void
    {
        if ($mode == self::MODE_PROCESS) {
            $this->processEvent($event);

            return;
        }

        $this->publishedEvents[] = $event;

        if ($mode == self::MODE_PROCESS_ALL) {
            $this->process();
        }
    }

    public function process(): void
    {
        while (count($this->publishedEvents) > 0) {
            $domainEvent = array_shift($this->publishedEvents);
            $this->processEvent($domainEvent);
        }

        $this->publishedEvents = [];
    }

    public function reset(): void
    {
        $this->subscribers = [];
        $this->publishedEvents = [];
    }

    private function __construct()
    {
        $this->container = null;

        $this->reset();
    }

    private function processEvent(DomainEvent $event): void
    {
        $eventClass = get_class($event);

        foreach ($this->subscribers as $subscriberClass => $eventClasses) {
            if (in_array($eventClass, $eventClasses) || in_array('*', $eventClasses)) {
                $subscriber = $this->container()->get($subscriberClass);
                $subscriber->handle($event);
            }
        }
    }

    private function container(): ContainerInterface
    {
        if (!$this->container) {
            throw new \Exception('Container not set');
        }

        return $this->container;
    }
}
