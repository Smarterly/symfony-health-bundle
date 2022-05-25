<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Console\Factory\ResultFormatterFactory;

use Cushon\HealthBundle\Console\Factory\ResultFormatterFactory\StyledOutputFactory;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\ApplicationStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\TesterTrait;

final class StyledOutputFactoryTest extends TestCase
{
    use ProphecyTrait;
    use TesterTrait;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->input = new ArrayInput([]);

        $this->initOutput([
            'capture_stderr_separately' => true,
        ]);
    }

    public function testItReturnsAStyledOutputInstance(): void
    {
        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(true);
        /** @var HealthCheck $revealed */
        $revealed = $healthCheck->reveal();

        $applicationStatusFormatter = $this->prophesize(ApplicationStatus::class);
        $applicationStatusFormatter->format($revealed, Argument::type(SymfonyStyle::class))->shouldBeCalledOnce();

        $dependencyFormatter = $this->prophesize(Dependency::class);
        $dependencyFormatter->format($revealed, Argument::type(SymfonyStyle::class))->shouldBeCalledOnce();

        $styledOutputFactory = new StyledOutputFactory(
            $applicationStatusFormatter->reveal(),
            $dependencyFormatter->reveal()
        );
        $styledOutput = $styledOutputFactory->fromIO($this->getInput(), $this->getOutput());

        $styledOutput->format($revealed);
    }
}
