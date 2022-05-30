<?php

declare(strict_types=1);

namespace Tests\Unit\ApplicationHealth\HealthReport;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\Exception\NoDependencyStatuses;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\SimpleHealthReport;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class SimpleHealthReportTest extends TestCase
{
    use ProphecyTrait;

    public function testItThrowsAnExceptionIfThereAreNoDependencyStatuses(): void
    {
        $this->expectExceptionObject(NoDependencyStatuses::create());

        new SimpleHealthReport(true);
    }

    public function testItReturnsTheHealth(): void
    {
        /** @var DependencyStatus $dependencyStatus */
        $dependencyStatus = $this->prophesize(DependencyStatus::class)->reveal();
        $report = new SimpleHealthReport(true, $dependencyStatus);

        $this->assertTrue($report->isHealthy());
    }
}
