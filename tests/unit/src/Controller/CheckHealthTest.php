<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Controller\CheckHealth;
use Cushon\HealthBundle\Controller\Exception\HealthController;
use Cushon\HealthBundle\Encoder\Json\SafeJson;
use Cushon\HealthBundle\Formatter\Http;
use Cushon\HealthBundle\Formatter\Http\SimpleJson;
use Cushon\HealthBundle\Message\Query\HealthCheck\DefaultHealthCheckQuery;
use Cushon\HealthBundle\Message\QueryFactory\QueryFactory;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use Cushon\HealthBundle\Message\Result\HealthCheck\Unhealthy;
use Cushon\HealthBundle\QueryBus\HealthCheckQueryBus;
use Ergebnis\Json\Printer\Printer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

use function Safe\json_decode;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class CheckHealthTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsAValidJsonResponse(): void
    {
        $serviceName = 'some service';
        $query = new DefaultHealthCheckQuery();
        $queryFactory = $this->prophesize(QueryFactory::class);
        $queryFactory->createQuery()->willReturn($query);

        $result = new Healthy(new SimpleStatus($serviceName, true));

        $queryBus = $this->prophesize(HealthCheckQueryBus::class);
        $queryBus->handleHealthCheckQuery($query)->willReturn($result);

        $jsonFormatter = new SimpleJson(new SafeJson(new Printer()));
        $controller = new CheckHealth(
            $queryBus->reveal(),
            $queryFactory->reveal(),
            $jsonFormatter
        );

        $response = $controller();
        $data = json_decode($response->getContent());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $dependency = $data->dependencies[0];
        $this->assertSame($serviceName, $dependency->name);
        $this->assertTrue($dependency->healthy);
    }

    public function testItReturnsAnErrorStatusCodeWhenUnhealthy(): void
    {
        $serviceName = 'some service';
        $query = new DefaultHealthCheckQuery();
        $queryFactory = $this->prophesize(QueryFactory::class);
        $queryFactory->createQuery()->willReturn($query);

        $result = new Unhealthy(new SimpleStatus($serviceName, true));

        $queryBus = $this->prophesize(HealthCheckQueryBus::class);
        $queryBus->handleHealthCheckQuery($query)->willReturn($result);

        $jsonFormatter = new SimpleJson(new SafeJson(new Printer()));
        $controller = new CheckHealth(
            $queryBus->reveal(),
            $queryFactory->reveal(),
            $jsonFormatter
        );

        $response = $controller();
        $data = json_decode($response->getContent());

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $dependency = $data->dependencies[0];
        $this->assertSame($serviceName, $dependency->name);
        $this->assertTrue($dependency->healthy);
    }

    public function testItThrowsAHealthControllerException(): void
    {
        $queryFactory = $this->prophesize(QueryFactory::class);
        $queryBus = $this->prophesize(HealthCheckQueryBus::class);
        $formatter = $this->prophesize(Http::class);

        $exc = new RuntimeException('Test');

        $queryFactory->createQuery()->willThrow($exc);

        $controller = new CheckHealth(
            $queryBus->reveal(),
            $queryFactory->reveal(),
            $formatter->reveal()
        );

        $this->expectExceptionObject(HealthController::create($exc));

        $controller();
    }
}
