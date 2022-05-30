<?php

declare(strict_types=1);

namespace Tests\Unit\ApplicationHealth\Dependencies;

use Cushon\HealthBundle\ApplicationHealth\Dependencies\Exception\NoDependencyChecks;
use Cushon\HealthBundle\ApplicationHealth\Dependencies\HealthReportFactory\SimpleReportFactory;
use Cushon\HealthBundle\ApplicationHealth\Dependencies\SimpleDependencies;
use Cushon\HealthBundle\ApplicationHealth\Dependencies\Simpledependencies\HealthReportFactory;
use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class SimpleDependenciesTest extends TestCase
{
    use ProphecyTrait;

    public function testItThrowsAnExceptionIfThereAreNoDependencyChecks(): void
    {
        /** @var HealthReportFactory $healthReportFactory */
        $healthReportFactory = $this->prophesize(HealthReportFactory::class)->reveal();
        $this->expectExceptionObject(NoDependencyChecks::create());

        new SimpleDependencies($healthReportFactory);
    }

    public function testItReturnsAHealthyReport(): void
    {
        $dependencyChecker = $this->prophesize(DependencyCheck::class);
        $dependencyStatus = $this->prophesize(DependencyStatus::class);
        $dependencyStatus->isHealthy()->willReturn(true);
        /** @var DependencyStatus $status */
        $status = $dependencyStatus->reveal();

        $dependencyChecker->check()->willYield([$status]);

        $healthReport = new HealthReport\SimpleHealthReport(true, $status);

        $healthReportFactory = $this->prophesize(HealthReportFactory::class);
        $healthReportFactory->generateReport(true, $status)->willReturn($healthReport);

        $simpleDependencies = new SimpleDependencies(
            $healthReportFactory->reveal(),
            $dependencyChecker->reveal()
        );
        $this->assertSame($healthReport, $simpleDependencies->check());
    }

    public function testItIsMarkedAsUnhealthyIfADependencyIsUnhealthy(): void
    {
        $dependencyChecker1 = $this->prophesize(DependencyCheck::class);
        $dependencyChecker2 = $this->prophesize(DependencyCheck::class);

        $dependencyStatus1 = $this->prophesize(DependencyStatus::class);
        $dependencyStatus1->isHealthy()->willReturn(false);
        $dependencyStatus2 = $this->prophesize(DependencyStatus::class);
        $dependencyStatus2->isHealthy()->willReturn(true);

        $status1 = $dependencyStatus1->reveal();
        $status2 = $dependencyStatus2->reveal();

        $dependencyChecker1->check()->willYield([$status1]);
        $dependencyChecker2->check()->willYield([$status2]);

        $healthReportFactory = new SimpleReportFactory();

        $simpleDependencies = SimpleDependencies::fromIterable(
            $healthReportFactory,
            [
                $dependencyChecker1->reveal(),
                $dependencyChecker2->reveal()
            ]
        );

        $report = $simpleDependencies->check();

        $this->assertFalse($report->isHealthy());
    }

    public function testItReturnsTheDependencies(): void
    {
        $dependencyChecker1 = $this->prophesize(DependencyCheck::class);
        $dependencyChecker2 = $this->prophesize(DependencyCheck::class);

        $dependencyStatus1 = $this->prophesize(DependencyStatus::class);
        $dependencyStatus1->isHealthy()->willReturn(false);
        $dependencyStatus2 = $this->prophesize(DependencyStatus::class);
        $dependencyStatus2->isHealthy()->willReturn(true);

        $status1 = $dependencyStatus1->reveal();
        $status2 = $dependencyStatus2->reveal();

        $dependencyChecker1->check()->willYield([$status1]);
        $dependencyChecker2->check()->willYield([$status2]);

        $healthReportFactory = new SimpleReportFactory();

        $simpleDependencies = SimpleDependencies::fromIterable(
            $healthReportFactory,
            [
                $dependencyChecker1->reveal(),
                $dependencyChecker2->reveal()
            ]
        );

        $report = $simpleDependencies->check();

        $dependencies = iterator_to_array($report->dependencies());

        $this->assertSame([$status1, $status2], $dependencies);
    }
}
