<?php

declare(strict_types=1);

namespace Tests\Unit\Handler\CheckHealth;

use Cushon\HealthBundle\ApplicationHealth\Dependencies;
use Cushon\HealthBundle\ApplicationHealth\Exception\ApplicationHealthError;
use Cushon\HealthBundle\ApplicationHealth\HealthReport;
use Cushon\HealthBundle\Handler\CheckHealth\DefaultHealthCheck;
use Cushon\HealthBundle\Handler\CheckHealth\Exception\ApplicationHealthCheckFailure;
use Cushon\HealthBundle\Handler\CheckHealth\Logger;
use Cushon\HealthBundle\Handler\CheckHealth\ResultFactory;
use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class DefaultHealthCheckTest extends TestCase
{
    use ProphecyTrait;

    public function testItLogsProgress(): void
    {
        $query = $this->prophesize(HealthCheckQuery::class)->reveal();
        $health = $this->prophesize(HealthReport::class)->reveal();
        $result = $this->prophesize(HealthCheckResult::class);

        $dependencies = $this->prophesize(Dependencies::class);
        $dependencies->check()->willReturn($health);

        $logger = $this->prophesize(Logger::class);

        $resultFactory = $this->prophesize(ResultFactory::class);
        $resultFactory->fromHealth($health)->willReturn($result);

        $handler = new DefaultHealthCheck(
            $dependencies->reveal(),
            $logger->reveal(),
            $resultFactory->reveal()
        );

        $handler($query);

        $logger->begin($query)->shouldHaveBeenCalled();
        $logger->complete($result)->shouldHaveBeenCalled();
    }

    public function testItThrowsAnExcpetionIfTheDependenciesCheckErrors(): void
    {
        $query = $this->prophesize(HealthCheckQuery::class)->reveal();
        $dependenciesException = new class ('test') extends Exception implements ApplicationHealthError {
        };

        $dependencies = $this->prophesize(Dependencies::class);
        $dependencies->check()->willThrow($dependenciesException);

        $logger = $this->prophesize(Logger::class);
        $logger->begin($query)->shouldBeCalled();
        $logger->error(Argument::type(ApplicationHealthCheckFailure::class))->shouldBeCalled();

        $resultFactory = $this->prophesize(ResultFactory::class);

        $handler = new DefaultHealthCheck(
            $dependencies->reveal(),
            $logger->reveal(),
            $resultFactory->reveal()
        );


        $this->expectExceptionObject(ApplicationHealthCheckFailure::fromApplicationHealthError(
            $dependenciesException
        ));

        $handler($query);
    }
}
