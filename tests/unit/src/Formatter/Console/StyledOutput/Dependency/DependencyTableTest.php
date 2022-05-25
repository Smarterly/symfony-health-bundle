<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Console\StyledOutput\Dependency;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\SimpleMapper;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable\DependencyRowFactory;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable\TableHeaderFactory;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\TesterTrait;

final class DependencyTableTest extends TestCase
{
    use TesterTrait;

    public function testItRendersHeaders(): void
    {
        $this->input = new ArrayInput([]);
        $this->initOutput([
            'capture_stderr_separately' => true,
        ]);

        $serviceName = 'some service';
        $symfonyStyler = new SymfonyStyle($this->getInput(), $this->getOutput());
        $dependency = new SimpleStatus($serviceName, false, 'Uh oh');
        $healthy = new Healthy($dependency);

        $simpleDependencyMapper = new SimpleMapper();
        $dependencyTable = new DependencyTable(
            new TableHeaderFactory($simpleDependencyMapper),
            new DependencyRowFactory($simpleDependencyMapper)
        );

        $dependencyTable->format($healthy, $symfonyStyler);

        $display = $this->getDisplay();

        $this->assertStringContainsString('Dependencies', $display);
        $this->assertStringContainsString('Name', $display);
        $this->assertStringContainsString('Status', $display);
        $this->assertStringContainsString('Info', $display);
    }

    public function testItRendersHealthyDependencies(): void
    {
        $this->input = new ArrayInput([]);
        $this->initOutput([
            'capture_stderr_separately' => true,
        ]);

        $serviceName = 'some service';
        $symfonyStyler = new SymfonyStyle($this->getInput(), $this->getOutput());
        $dependency = new SimpleStatus($serviceName, true, 'Life is good');
        $healthy = new Healthy($dependency);

        $simpleDependencyMapper = new SimpleMapper();
        $dependencyTable = new DependencyTable(
            new TableHeaderFactory($simpleDependencyMapper),
            new DependencyRowFactory($simpleDependencyMapper)
        );

        $dependencyTable->format($healthy, $symfonyStyler);

        $display = $this->getDisplay();

        $this->assertStringContainsString($serviceName, $display);
    }
}
