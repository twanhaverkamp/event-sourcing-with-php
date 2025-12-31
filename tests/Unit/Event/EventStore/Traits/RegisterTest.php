<?php

declare(strict_types=1);

namespace TwanHaverkamp\EventSourcingWithPhp\Tests\Unit\Event\EventStore\Traits;

use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;
use TwanHaverkamp\EventSourcingWithPhp\Example;
use TwanHaverkamp\EventSourcingWithPhp\Event\EventStore\Traits;

#[Attributes\CoversTrait(Traits\Register::class)]
class RegisterTest extends TestCase
{
    use Traits\Register;

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'register\' keeps passed Event classes in memory')]
    public function registerKeepsEventInMemory(): void
    {
        $eventClasses = [
            Example\Event\InvoiceWasCreated::class,
            Example\Event\PaymentTransactionWasStarted::class,
            Example\Event\PaymentTransactionWasCompleted::class,
            Example\Event\PaymentTransactionWasCancelled::class,
        ];

        $this->register(...$eventClasses);

        static::assertSame($eventClasses, static::$registeredEventClasses);
    }

    #[Attributes\Test]
    #[Attributes\TestDox('Assert that \'register\' filters out non-Event classes')]
    public function registerFiltersNonEventClasses(): void
    {
        /** @phpstan-ignore argument.type */
        $this->register('invalid-event-class');

        static::assertSame([], static::$registeredEventClasses);
    }

    protected function tearDown(): void
    {
        static::$registeredEventClasses = [];
    }
}
