# Event Sourcing with PHP

**Table of Contents**

- [What problem does Event Sourcing solve for you?](#what-problem-does-event-sourcing-solve-for-you)
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

### Drawbacks
While Event Sourcing may solve problems, it also brings some challenges with it:

- Learning curve; *When shifting from CRUD to Event Sourcing, you may experience a steep learning curve.*
- Potentially slow; *Especially when your aggregate has a long life cycle.*

<a href="#considerations"></a>
### Considerations
Since this is supposed to be a *lightweight* library you will have to come up (for now) with a solution
for the following:

- [ ] Snapshots; *Cache your aggregate with a "Snapshot event" to reduce loading time.*
- [ ] Projections; *When you're working with relational databases.*
- [ ] Anonymize; *Protect (privacy) sensitive data.*

> Yes, I'm planning to implement these features **soon™**, but until then it's up to you. 😅

## Components

### Aggregate
The [Aggregate](/src/Aggregate/AggregateInterface.php) encapsulates business logic and its public methods reflect
your domain.

### AggregateRootId
The [AggregateRootId](/src/Aggregate/AggregateRootId/AggregateRootIdInterface.php) is the Aggregate's unique identifier
and is created before the Aggregate is being stored.

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

## Contribute
You've found a bug or want to introduce a new feature? Awesome! 🤩

### Fork
Create a fork by clicking [this link](https://github.com/twanhaverkamp/event-sourcing-with-php/fork) and follow 
the instructions on that page.

Now you have the project copied into a new repository on your own [GitHub](https://github.com/) account.

> At this point I'm assuming you have a GitHub account.

### Run the project locally
After cloning the repository onto your computer you can run the project in [Docker](https://www.docker.com/) with
the following command:

```shell
# Start the project:
docker compose up -d
```
```shell
# Shell into the PHP container:
docker compose exec -it php-8.3 sh
```

> This requires you to have Docker installed on your computer. Personally I'm using
> [Docker Desktop](https://www.docker.com/products/docker-desktop/), but there are other alternatives like
> [Rancher Desktop](https://rancherdesktop.io/) out there as well, that's totally up to you.

### Testing
When you've fixed a bug or introduced a new feature you cover it with [PHPUnit](https://docs.phpunit.de/en/11.5/) tests
to make sure code quality won't decrease and more importantly; the code behaves as expected.

```shell
# Run PHPUnit:
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
