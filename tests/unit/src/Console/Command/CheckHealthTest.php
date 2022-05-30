<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Command;

use Cushon\HealthBundle\Console\Command\CheckHealth;
use Cushon\HealthBundle\Console\Exception\HealthConsole;
use Cushon\HealthBundle\Console\Factory\ResultFormatterFactory;
use Cushon\HealthBundle\Formatter\Console;
use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\QueryFactory\QueryFactory;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;
use Cushon\HealthBundle\QueryBus\HealthCheckQueryBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class CheckHealthTest extends TestCase
{
    use ProphecyTrait;

    public function testItConfiguresDefaultDescriptionAndName(): void
    {
        $consoleCommand = new CheckHealth(
            $this->prophesize(HealthCheckQueryBus::class)->reveal(),
            $this->prophesize(QueryFactory::class)->reveal(),
            $this->prophesize(ResultFormatterFactory::class)->reveal()
        );

        $this->assertSame(CheckHealth::DEFAULT_NAME, $consoleCommand->getName());
        $this->assertSame(CheckHealth::DEFAULT_DESCRIPTION, $consoleCommand->getDescription());
    }

    public function testItOutputsTheHealth(): void
    {
        $healthCheckQuery = $this->prophesize(HealthCheckQuery::class)->reveal();
        $healthCheckResult = $this->prophesize(HealthCheckResult::class)->reveal();

        $queryFactory = $this->prophesize(QueryFactory::class);
        $queryFactory->createQuery()->willReturn($healthCheckQuery);

        $queryBus = $this->prophesize(HealthCheckQueryBus::class);
        $queryBus->handleHealthCheckQuery($healthCheckQuery)->willReturn($healthCheckResult);

        $consoleFormatter = $this->prophesize(Console::class);
        $consoleFormatter->format($healthCheckResult)->shouldBeCalledOnce();
        $resultFormatterFactory = $this->prophesize(ResultFormatterFactory::class);
        $resultFormatterFactory->fromIO(
            Argument::type(InputInterface::class),
            Argument::type(OutputInterface::class),
        )->willReturn($consoleFormatter->reveal());

        $command = new CheckHealth(
            $queryBus->reveal(),
            $queryFactory->reveal(),
            $resultFormatterFactory->reveal()
        );

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();
    }

    public function testItThrowsAnExceptionIfTheHealthCheckFails(): void
    {
        $quwryBus = $this->prophesize(HealthCheckQueryBus::class);
        $queryFactory = $this->prophesize(QueryFactory::class);
        $resultFormatterFactory = $this->prophesize(ResultFormatterFactory::class);

        $exc = new RuntimeException('Test');

        $queryFactory->createQuery()->willThrow($exc);

        $command = new CheckHealth(
            $quwryBus->reveal(),
            $queryFactory->reveal(),
            $resultFormatterFactory->reveal()
        );

        $this->expectExceptionObject(HealthConsole::create($exc));

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
