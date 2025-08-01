# Contribute
You've found a bug or want to introduce a new feature? Awesome! 🤩

**Table of Contents**

- [Fork](#fork)
- [Run the project locally](#run-the-project-locally)
- [Testing](#testing)
- [Pull request](#pull-request)
- [Update your LinkedIn profile](#update-your-linkedin-profile)

## Fork
Create a fork by clicking [this link](https://github.com/twanhaverkamp/event-sourcing-with-php/fork) and follow
the instructions on that page.

Now you have the project copied into a new repository on your own [GitHub](https://github.com/) account.

> At this point I'm assuming you have a GitHub account.

## Run the project locally

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

## Testing
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

## Pull request
Every [git](https://git-scm.com/) "push" triggers a [GitHub Actions](https://github.com/features/actions) workflow
called "quick-tests" that runs the following jobs:

- [Composer Audit](https://getcomposer.org/doc/03-cli.md#audit)
- Coding standards ([PSR-12](https://www.php-fig.org/psr/psr-12/)) with [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer/)
- Static code analysis with [PHPStan](https://phpstan.org/)
- Unit tests with PHPUnit

If all checks pass ✅ you can create a pull request targeting this repository's `main` branch.
I'll review it as **soon™** as possible, I'll promise! 🤝🏻

## Update your LinkedIn profile
Now you're officially an *open-source software contributor*, thank you! ❤️  
Time to update your [LinkedIn](https://linkedin.com/) profile! 🏆
