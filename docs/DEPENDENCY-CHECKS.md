# Dependency Checks

## Theory of Operation

The bundle makes use of the Symfony Messenger component to query the health of a service or application. The health query is routed to a handler that creates a "Health Report" and returns this as a Health Check Result. The Result is then formatted based on whether the query was sent via a Console Command or API.

The default strategy for the bundle is the `Cushon\HealthBundle\ApplicationHealth\Dependencies\SimpleDependencies` class, which assumes that if one or more Dependencies are unhealthy, the application is Unhealthy. If you prefer more fine-grained decision-making over what constitutes health, you can write a different strategy that implements `Cushon\HealthBundle\ApplicationHealth\Dependencies`.

Out of the box, the Health bundle will error - because you haven't defined any Dependencies yet!

![](assets/img/screenshots/no-dependency-checks-registered.png)

## What is a Dependency?

A _dependency_ in the context of the bundle is a high-level component underpinning capabilities of a service or application.

By the time your first feature or unit of functionality goes into production, you should have identified what dependencies need to be in place for it, and how to ensure you know they are still working _before_ they are needed. The Health bundle allows you to query these dependencies and see their health, rather than waiting for a problem to cause an alert.

Individual examples of separate dependencies include:

- Accessing a database with a read user
- Writing to a database table with a write user (assuming two separate users for design patterns such as [Command Query Segregation][command-query-segragation]).
- Contacting a partner API
- Retrieving a file from an S3 bucket.
- Able to send a message on a queue.

In all cases, a dependency check should:
- _be non-destructive_ - ie, don't test a write database user with a `TRUNCATE customers;` command.
- _have well known effects_ - does your partner API charge per query? If so, do you want to be checking every minute, 24/7?

## Writing a Dependency Check

All Dependency Checks should implement the interface `Cushon\HealthBundle\ApplicationHealth\DependencyCheck`. If you have `autoconfiguration` enabled in your Symfony project, then this is enough to have it picked up by the bundle, which will tag it as a `cushon_health.dependency_check`. The default Health Report strategy `SimpleDependencies` leverages Symfony's ability to pass tagged services to a Service Locator to automatically register the Dependency Check in the constructor.

### Example Dependency Checks

#### A Database Dependency Check Using Doctrine

In this example, we use the well-known Doctrine Database Abstraction Layer (DBAL) to test that all the users can read and write to the database as required. This example requires the Doctrine migrations to be run before it will behave as expected. You should also bring up the docker services, which will populate an empty schema and two users (read and write) for you:
```bash
> make up
Bringing up services...
Creating network "cushon-health-bundle_app" with the default driver
Creating cushon-health-bundle_nginx_1     ... done
Creating cushon-health-bundle_bundle_1    ... done
Creating cushon-health-bundle_db_1        ... done
Creating cushon-health-bundle_blackfire_1 ... done

> app/bin/console doctrine:migrations:migrate
WARNING! You are about to execute a migration in database "cushon_health_test" that could result in schema changes and data loss. Are you sure you wish to continue? (yes/no) [yes]:
> y

[notice] Migrating up to App\DoctrineMigrations\Version20220530155854
[notice] finished in 77.9ms, used 16M memory, 1 migrations executed, 1 sql queries
```
The above creates a very simple table called `health` with one column `last_checked`. Obviously, the read and write user should be the same users used for core application functionality, or you may miss that there actually is a problem.

Using Symfony's autwiring and the Symfony Doctrine bundle, let's create a simple configuration in `doctrine.yaml`:

```yaml
---
parameters:
  env(DB_READ_HOST):      '127.0.0.1'
  env(DB_READ_NAME):      'cushon_health_test'
  env(DB_READ_USER):      'cushon_read'
  env(DB_READ_VERSION):   'mariadb-10.7.3'
  env(DB_READ_PORT):      33010

  env(DB_WRITE_HOST):     '127.0.0.1'
  env(DB_WRITE_NAME):     'cushon_health_test'
  env(DB_WRITE_USER):     'cushon_write'
  env(DB_WRITE_VERSION):  'mariadb-10.7.3'
  env(DB_WRITE_PORT):     33010 

doctrine:
  dbal:
    default_connection: 'read'
    connections:
      read:
        # Connection used for read operations
        host:             '%env(string:DB_READ_HOST)%'
        dbname:           '%env(string:DB_READ_NAME)%'
        user:             '%env(string:DB_READ_USER)%'
        password:         '%env(string:DB_READ_PASSWORD)%'
        server_version:   '%env(string:DB_READ_VERSION)%'
        port:             '%env(int:DB_READ_PORT)%'
        driver:           'pdo_mysql'

      write:
        # Connection used for write operations
        host:             '%env(string:DB_WRITE_HOST)%'
        dbname:           '%env(string:DB_WRITE_NAME)%'
        user:             '%env(string:DB_WRITE_USER)%'
        password:         '%env(string:DB_WRITE_PASSWORD)%'
        server_version:   '%env(string:DB_WRITE_VERSION)%'
        port:             '%env(int:DB_WRITE_PORT)%'
        driver:           'pdo_mysql'
```
Here we use environmentals to keep it inline with the 12-Factor Application principle, but add some sensible defaults to the environment variables.

Next, we create two very simple wrappers to test the connections:

```php
<?php

declare(strict_types=1);

namespace App\Repository\DatabaseCheck;

use App\ApplicationHealth\DependencyCheck\DatabaseCheck\DatabaseUserCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;

final class WriteUserCheck implements DatabaseUserCheck
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @param Connection $writeConnection
     */
    public function __construct(Connection $writeConnection)
    {
        $this->connection = $writeConnection;
    }

    /**
     * @inheritDoc
     */
    public function checkUser(): DependencyStatus
    {
        $health = false;
        try {
            $this->connection->createQueryBuilder()
                ->insert('health')
                ->setValue('last_checked', ':now')
                ->setParameter('now', new DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
                ->executeStatement();
            $health = true;
            $info = null;
        } catch (Exception $e) {
            $info = $e->getMessage();
        }

        return new SimpleStatus('DB write user', $health, $info);
    }
}
```
We will skip the example of the read user check for the sake of keeping this example less verbose,but the class exists in the example app.

Next, we tag all the repositories implementing the `DatabaseUserCheck` interface, so we can easily pass them to our database check. Updating our `services.yaml` with an `_instanceof` service definition:

```yaml
  _instanceof:
      App\ApplicationHealth\DependencyCheck\DatabaseCheck\DatabaseUserCheck:
          tags:
              - 'app.health.check.db'

  App\ApplicationHealth\DependencyCheck\DatabaseCheck:
      arguments:
          - !tagged_iterator app.health.check.db
```
Our CQRS-style health check can now be fairly straightforward. We add a few guards to ensure that it must have at least one database check in the constructor:

```php
<?php

declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck;

use App\ApplicationHealth\DependencyCheck\DatabaseCheck\DatabaseUserCheck;
use App\ApplicationHealth\DependencyCheck\DatabaseCheck\Exception\NoDatabaseChecksProvided;
use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Ds\Set;
use Generator;

final class DatabaseCheck implements DependencyCheck
{
    /**
     * @var Set<DatabaseUserCheck>
     */
    private Set $dependencyRepositories;

    /**
     * @param iterable<DatabaseUserCheck> $dependencyRepositories
     */
    public function __construct(iterable $dependencyRepositories)
    {
        $this->dependencyRepositories = new Set();
        foreach ($dependencyRepositories as $dependencyRepository) {
            $this->addDatabaseUserCheck($dependencyRepository);
        }

        if (!$this->dependencyRepositories->count()) {
            throw NoDatabaseChecksProvided::create();
        }
    }

    /**
     * @inheritDoc
     */
    public function check(): Generator
    {
        foreach ($this->dependencyRepositories as $dependencyRepository) {
            yield $dependencyRepository->checkUser();
        }
    }

    /**
     * @param DatabaseUserCheck $databaseUserChecks
     * @return void
     */
    private function addDatabaseUserCheck(DatabaseUserCheck $databaseUserChecks): void
    {
        $this->dependencyRepositories->add($databaseUserChecks);
    }
}

```



#### An API Dependency Check

In this example, we're assuming that the application has a dependency on a remote API (in this case, the seminal [random.org][random.org]). 

Let's create a simple Dependency check that will check that we can draw a single random number from the random.org API. We will continue to use [Repository pattern][repository-pattern]:

```php
<?php

declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck;

use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\Exception\HealthDependencyException;
use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\HealthDependencyRepository;
use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;

final class RandomOrgApiCheck implements DependencyCheck
{
    private HealthDependencyRepository $healthDependencyRepository;

    public function __construct(HealthDependencyRepository $healthDependencyRepository)
    {
        $this->healthDependencyRepository = $healthDependencyRepository;
    }

    /**
     * @inheritDoc
     */
    public function check(): DependencyStatus
    {
        $health = false;

        try {
            $randomNumber = $this->healthDependencyRepository->fetchRandomNumber();
            $health = true;
            $info = sprintf('All OK, random number was %d', $randomNumber);
        } catch (HealthDependencyException $e) {
            $info = $e->getMessage();
        }

        return new SimpleStatus('random.org', $health, $info);
    }
}
```

Next, we will use the Symfony framework's ability to have Scoped HTTP clients to set up some configuration for the Symfony HTTP Client:

```yaml
# app/config/packages/framework.yaml
---
framework:
# ... Other settings
  http_client:
    scoped_clients:
      random.client:
      base_uri: 'https://www.random.org'
```

Finally, we will create a Repository that uses the above, throwing exceptions if the response is not "healthy":

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\HealthDependencyRepository;
use App\Repository\Exception\ResponseNotOk;
use App\Repository\Exception\SymfonyHttpException;
use App\Repository\Exception\UnknownApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

final class RandomApiHealth implements HealthDependencyRepository
{
    public const DEFAULT_MIN = 1;

    private HttpClientInterface $httpClient;
    private int $min;

    /**
     * @param HttpClientInterface $randomClient
     * @param int $min
     */
    public function __construct(HttpClientInterface $randomClient, int $min = self::DEFAULT_MIN)
    {
        $this->httpClient = $randomClient;
        $this->min = $min;
    }

    /**
     * @inheritDoc
     */
    public function fetchRandomNumber(): int
    {
        try {
            $response = $this->httpClient->request(
                Request::METHOD_GET,
                '/integers/',
                [
                    'query' => $this->getQueryParams(),
                ]
            );
        } catch (ExceptionInterface $e) {
            throw SymfonyHttpException::fromPrevious($e);
        } catch (Throwable $e) {
            throw UnknownApiException::create($e);
        }

        return $this->parseResponse($response);
    }

    /**
     * @param ResponseInterface $response
     * @return int
     */
    private function parseResponse(ResponseInterface $response): int
    {
        $statusCode = $response->getStatusCode();
        if (Response::HTTP_OK !== $statusCode) {
            throw ResponseNotOk::fromResponseCode($statusCode);
        }

        return (int) $response->getContent();
    }

    /**
     * @return array
     */
    private function getQueryParams(): array
    {
        # https://www.random.org/integers/?num=10&min=1&max=6&col=1&base=10&format=plain&rnd=new
        return [
            'num' => 1,
            'min' => $this->min,
            'max' => 6,
            'col' => 1,
            'base' => 10,
            'format' => 'plain',
            'rnd' => 'new'
        ];
    }
}
```

[command-query-segragation]: https://en.wikipedia.org/wiki/Command%E2%80%93query_separation
[random.org]: https://random.org
[repository-pattern]: https://deviq.com/design-patterns/repository-pattern
