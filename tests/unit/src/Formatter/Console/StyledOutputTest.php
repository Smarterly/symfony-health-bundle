<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Console;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Console\Factory\ResultFormatterFactory\StyledOutputFactory;
use Cushon\HealthBundle\Formatter\Console\StyledOutput;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\ApplicationStatus\StatusSection;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable\DependencyRowFactory;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable\TableHeaderFactory;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\SimpleMapper;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\TesterTrait;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class StyledOutputTest extends TestCase
{
    use ProphecyTrait;
    use TesterTrait;

    private StyledOutputFactory $styledOutputFactory;

    protected function setUp(): void
    {
        $this->input = new ArrayInput([]);

        $this->initOutput([
            'capture_stderr_separately' => true,
        ]);

        $simpleDependencyMapper = new SimpleMapper();
        $this->styledOutputFactory = new StyledOutputFactory(
            new StatusSection(),
            new DependencyTable(
                new TableHeaderFactory($simpleDependencyMapper),
                new DependencyRowFactory($simpleDependencyMapper)
            )
        );
    }

    public function testItFormatsAHealthyReport(): void
    {
        $serviceName1 = 'Fake DB read user';
        $serviceName2 = 'Fake DB write user';

        $dependencyStatus1 = new SimpleStatus($serviceName1, true);
        $dependencyStatus2 = new SimpleStatus($serviceName2, true);

        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(true);
        $healthCheck->dependencies()->willReturn([
            $dependencyStatus1,
            $dependencyStatus2
        ]);

        $outFormatter = $this->createStyledOutput();
        $outFormatter->format($healthCheck->reveal());

        $output = $this->getDisplay();

        $this->assertStringContainsString('[OK] Status: Healthy', $output);

        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('Status', $output);
        $this->assertStringContainsString($serviceName1, $output);
        $this->assertStringContainsString($serviceName2, $output);
    }

    public function testItFormatsAnUnhealthyReport(): void
    {
        $serviceName1 = 'Fake DB read user';
        $serviceName2 = 'Fake DB write user';

        $dependencyStatus1 = new SimpleStatus($serviceName1, false);
        $dependencyStatus2 = new SimpleStatus($serviceName2, true);

        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(false);
        $healthCheck->dependencies()->willReturn([
            $dependencyStatus1,
            $dependencyStatus2
        ]);

        $outFormatter = $this->createStyledOutput();

        $outFormatter->format($healthCheck->reveal());

        $output = $this->getErrorOutput();

        $this->assertStringContainsString('[WARNING] Status: Unhealthy', $output);

        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('Status', $output);
        $this->assertStringContainsString($serviceName1, $output);
        $this->assertStringContainsString($serviceName2, $output);
    }

    /**
     * @return StyledOutput
     */
    private function createStyledOutput(): StyledOutput
    {
        return $this->styledOutputFactory->fromIO(
            $this->getInput(),
            $this->getOutput()
        );
    }
}
