# Event Sourcing with PHP

**Table of Contents**

- [What problem does Event Sourcing solve for you?](#what-problem-does-event-sourcing-solve-for-you)
    - [Compare data structures](#compare-data-structures)
    - [Drawbacks](#drawbacks)
    - [Considerations](#considerations)

- [Components](#components)
    - [Aggregate](#aggregate)
    - [AggregateRootId](#aggregaterootid)
    - [Event](#event)
    - [EventStore](#eventstore)

- [Usage](#usage)
    - [Installation](#installation)
    - [Implementation](#implementation)
      - [Create an Aggregate class](#create-an-aggregate-class)
      - [Create your Event classes](#create-your-event-classes)
      - [Record Events with your Aggregate](#record-events-with-your-aggregate)
      - [Store the Aggregate](#save-the-aggregate)
      - [Load the Aggregate](#load-the-aggregate)

- [Contribute](#contribute)
    - [Fork](#fork)
    - [Run the project locally](#run-the-project-locally)
    - [Testing](#testing)
    - [Pull request](#pull-request)
    - [Update your LinkedIn profile](#update-your-linkedin-profile)

## What problem does Event Sourcing solve for you?
You not only want to know what the current state of an object is, but you also want to know
*how* the object got in this state? In that case Event Sourcing might be the solution
to your problem!

### Compare data structures

**CRUD with relations:**

| ID | Number | Subtotal | Tax  | Total | CreatedAt  | PaymentDueAt |
|----|--------|----------|------|-------|------------|--------------|
| 1  | 12-34  | 22.80    | 3.75 | 16.55 | 2025-02-01 | 2025-03-01   |

| ID | Invoice ID | Reference    | Description | Quantity | Price | Tax   |
|----|------------|--------------|-------------|----------|-------|-------|
| 1  | 1          | prod.123.456 | Product     | 3        | 5.95  | 21.00 |
| 2  | 1          |              | Shipping    | 1        | 4.95  | 0.00  |

| ID | Invoice ID | PaymentMethod | Amount | Status    |
|----|------------|---------------|--------|-----------|
| 1  | 1          | Manual        | 10.00  | Completed |

**Event sourced:**

| AggregateRootId | Event                             | Payload                          | RecordedAt |
|-----------------|-----------------------------------|----------------------------------|------------|
| 01941d8f-995... | invoice-was-created               | {"number": "12-34", "items": []} | 2025-02-01 | 
| 01941d8f-995... | payment-transaction-was-started   | {"id": 1, amount": 10.00}        | 2025-02-01 |
| 01941d8f-995... | payment-transaction-was-completed | {"id": 1}                        | 2025-02-01 |

Your read models can, of course, still be stored in relational tables as illustrated above,
but your Aggregate will be built based on the stored Events.

### Drawbacks
While Event Sourcing may solve problems, it also brings some challenges with it:

- Learning curve; *When shifting from CRUD to Event Sourcing, you may experience a steep learning curve.*
- Potentially slow; *Especially when your aggregate has a long life cycle.*

### Considerations
Since this is supposed to be a *lightweight* library you will have to come up (for now) with a solution
for the following:

- [ ] Snapshots; *Cache your aggregate with a "Snapshot event" to reduce loading time.*
- [ ] Projections; *For working with read models.*
- [ ] Anonymize; *Protect (privacy) sensitive data.*

> Yes, I'm planning to implement these features **soon™**, but until then it's up to you. 😅

## Components

### Aggregate
The [Aggregate](/src/Aggregate/AggregateInterface.php) encapsulates business logic and its public methods reflect
your domain.

### AggregateRootId
The [AggregateRootId](/src/Aggregate/AggregateRootId/AggregateRootIdInterface.php) is the Aggregate's unique identifier,
which is instantiated before the Aggregate is being created.

**Available:**
- [UUID v7](/src/Aggregate/AggregateRootId/Uuid7.php); *wraps the [ramsey/uuid](https://uuid.ramsey.dev/) library.*

### Event
An [Event](/src/Event/EventInterface.php) changed one or multiple properties of your Aggregate. New property values
are "stored" in its payload and will be re-applied when the Aggregate is being rebuilt from storage.

As an Event took place in the past, it's considered good practice to reflect this when naming your Events.

### EventStore
The [EventStore](/src/Event/EventStore/EventStoreInterface.php) is an interesting one. Instead of fetching an Aggregate
directly from your storage you query it's related Events with the AggregateRootId sorted by their "recordedAt" value
in ascending order. Each Event will be applied to the Aggregate, which eventually will get in it's expected state.

## Usage

### Installation

**Requirements:**
- PHP 8.3 (or higher)

If you're using [Composer](https://getcomposer.org/) in your project you can run the following command:

```shell
composer require twanhaverkamp/event-sourcing-with-php:^1.0 
```

### Implementation
To understand how to implement this library in your project I would encourage you to take a look at
the [/example](/example) directory and specifically the [Invoice](/example/Aggregate/Invoice.php) class
as it represents an aggregate containing both business logic and the usage of events.

> You'll see some `// ...` in the code snippets. It indicates there's more code, but it's not relevant
> for the given example.

#### Create an Aggregate class
Add public methods for your business logic, where their names reflect your domain.

```php
<?php

// ...

use DateTimeImmutable;
use DateTimeInterface;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event;

class Invoice extends Aggregate\AbstractAggregate
{
    public string $number;

    /**
     * @var DTO\Item[]
     */
    public array $items;

    /**
     * @var DTO\PaymentTransaction[]
     */
    public array $paymentTransactions;

    public DateTimeInterface $createdAt;

    public static function init(string $aggregateRootId): self
    {
        return new self(
            AggregateRootId\Uuid7::fromString($aggregateRootId),
        );
    }

    public static function create(string $number, DTO\Item ...$items): self
    {
        $invoice = new self($aggregateRootId = new AggregateRootId\Uuid7());
        $this->number    = $event->number;
        $this->items     = $event->items;
        $this->createdAt = new DateTimeImmutable();

        return $invoice;
    }

    public function startPaymentTransaction(string $paymentMethod, float $amount): DTO\PaymentTransaction
    {
        // ...
    }

    // ...
}
```

> I would recommend you to add a *static* `init` method that expects a string value as AggregateRootId. This method
> returns an empty Aggregate with the correct AggregateRootId instance type that you can use to pass to an EventStore’s
> `load` function.

#### Create your Event classes
For every method that affects the Aggregate you create an Event class.

```php
<?php

// ...

use DateTimeInterface;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event;

readonly class InvoiceWasCreated extends Event\AbstractEvent
{
    /**
     * @param DTO\Item[] $items
     */
    public function __construct(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        public string $number,
        public array $items,
        public DateTimeInterface $createdAt,
    ) {
        parent::__construct($aggregateRootId, $createdAt);
    }

    public static function fromPayload(
        AggregateRootId\AggregateRootIdInterface $aggregateRootId,
        array $payload,
        DateTimeInterface $recordedAt,
    ): self {
        return new self(
            $aggregateRootId,
            (string)$payload['number'],
            array_map(fn (array $item) => DTO\Item::fromArray($item), $payload['items']),
            $recordedAt,
        );
    }

    public function getPayload(): array
    {
        return [
            'number' => $this->number,
            'items'  => array_map(fn (DTO\Item $item) => $item->toArray(), $this->items),
        ];
    }
}
```

> The `getPayload` and `fromPayload` methods are used by the EventStore to store- and load an Event.
> An Event is supposed to be immutable and therefore should be *readonly*. 

#### Record Events with your Aggregate
Add an `apply[event-name]` method for every Event and replace your domain logic with a `recordThat` call.
Move the domain logic to its designated `apply[event-name]` method.

```php
<?php

use DateTimeImmutable;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate;
use TwanHaverkamp\EventSourcingWithPhp\Aggregate\AggregateRootId;
use TwanHaverkamp\EventSourcingWithPhp\Event;
use TwanHaverkamp\EventSourcingWithPhp\Event\Exception;

class Invoice extends Aggregate\AbstractAggregate
{
    // ...

    public static function create(string $number, DTO\Item ...$items): self
    {
        $invoice = new self($aggregateRootId = new AggregateRootId\Uuid7());
        $invoice->recordThat(new InvoiceWasCreated(
            $aggregateRootId,
            $number,
            $items,
            new DateTimeImmutable()
        ));

        return $invoice;
    }

    // ...

    public function apply(Event\EventInterface $event): void
    {
        match ($event::class) {
            InvoiceWasCreated::class => $this->applyInvoiceWasCreated($event),
            // ...
            default => throw new Exception\EventNotSupportedException(
                message: sprintf(
                    'Event "%s" is not supported by "%s" aggregate.',
                    $event::class,
                    $this::class,
                ),
            ),
        };
    }

    // ...

    private function applyInvoiceWasCreated(InvoiceWasCreated $event): void
    {
        $this->number    = $event->number;
        $this->items     = $event->items;
        $this->createdAt = clone $event->createdAt;
    }

    // ...
}
```

> The `recordThat` method is part of the [AbstractAggregate](/src/Aggregate/AbstractAggregate.php) class and requires
> you to add an `apply` method that receives an Event as argument. With a *match* you can map each Event to the correct
> `apply[event-name]` method.

#### Save the Aggregate
This requires you to create your own EventStore that implements
the [EventStoreInterface](/src/Event/EventStore/EventStoreInterface.php).

```php
<?php

// ...

use TwanHaverkamp\EventSourcingWithPhp\Event\EventStore;

// ...

$invoice = Invoice::create('12-34',
    new DTO\Item('prod.123.456', 'Product', 3, 5.95, 21.),
    new DTO\Item(null, 'Shipping', 1, 4.95, 0.),
);

$invoice->startPaymentTransaction('Manual', 10.);

// ...

/** @var EventStore\EventStoreInterface $eventStore */
$eventStore = // ...

// $invoice->aggregateRootId = AggregateRootId\Uuid7<'01941d8f-9951-72af-b5ce-5aa7aa23ea68'>

$eventStore->save($invoice);

// ...
```

#### Load the Aggregate
When loading an Aggregate its Events are applied one-by-one by the EventStore based on the related AggregateRootId
sorted by `recordedAt` in ascending order.

```php
<?php

// ...

use TwanHaverkamp\EventSourcingWithPhp\Event\EventStore;

// ...

$invoice = Invoice::init('01941d8f-9951-72af-b5ce-5aa7aa23ea68');

/** @var EventStore\EventStoreInterface $eventStore */
$eventStore = // ...

$eventStore->load($invoice);

// $invoice->number = '12-34'
// $invoice->items = [
//     DTO\Item(reference: 'prod.123.456', description: 'Product', quantity: 3, price: 5.95, tax: 21.),
//     DTO\Item(description: 'Shipping', quantity: 1, price: 4.95, tax: 0.),
// ]
// $invoice->paymentTransactions = [
//     DTO\PaymentTransaction(paymentMethod: 'Manual', amount: 10., status: 'started'),
// ]

// ...
```

## Contribute
You've found a bug or want to introduce a new feature? Awesome! 🤩

### Fork
Create a fork by clicking [this link](https://github.com/twanhaverkamp/event-sourcing-with-php/fork) and follow 
the instructions on that page.

Now you have the project copied into a new repository on your own [GitHub](https://github.com/) account.

> At this point I'm assuming you have a GitHub account.

### Run the project locally

```shell
# Navigate to your working directory
cd ../[your-working-directory]

# Clone the project from Github
git clone git@github.com:[your-github-username]/event-sourcing-with-php.git

# Navigate to the project directory
cd event-sourcing-with-php/

# Start Docker Compose
docker compose up -d
```

If you want to get into the project's PHP container, run the following command:

```shell
docker compose exec -it php-8.3 sh
```

> This requires you to have [Docker](https://www.docker.com/) installed on your computer. Personally I'm using
> [Docker Desktop](https://www.docker.com/products/docker-desktop/), but there are other alternatives like
> [Rancher Desktop](https://rancherdesktop.io/) out there as well, it's totally up to you.

### Testing
When you've fixed a bug or introduced a new feature you cover it with [PHPUnit](https://docs.phpunit.de/en/11.5/) tests
to make sure code quality won't decrease and more importantly; the code behaves as expected.

```shell
# Run PHP CodeSniffer
docker compose exec php-8.3 vendor/bin/phpcs example src tests --standard=PSR12
```

```shell
# Run PHPStan
docker compose exec php-8.3 vendor/bin/phpstan analyse example src tests --level=9
```

```shell
# Run PHPUnit
docker compose exec php-8.3 vendor/bin/phpunit
```

### Pull request
Every [git](https://git-scm.com/) "push" triggers a [GitHub Actions](https://github.com/features/actions) workflow
called "quick-tests" that runs the following jobs:

- [Composer Audit](https://getcomposer.org/doc/03-cli.md#audit)
- Coding standards ([PSR-12](https://www.php-fig.org/psr/psr-12/)) with [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer/)
- Static code analysis with [PHPStan](https://phpstan.org/)
- Unit tests with PHPUnit

If all checks pass ✅ you can create a pull request targeting this repository's `main` branch.
I'll review it as **soon™** as possible, I'll promise! 🤝🏻

### Update your LinkedIn profile
Now you're officially an *open-source software contributor*, thank you! ❤️  
Time to update your [LinkedIn](https://linkedin.com/) profile! 🏆
