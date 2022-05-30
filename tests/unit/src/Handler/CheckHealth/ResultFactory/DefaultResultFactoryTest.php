<?php

declare(strict_types=1);

namespace Tests\Unit\Handler\CheckHealth\ResultFactory;

use Cushon\HealthBundle\ApplicationHealth\HealthReport;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Handler\CheckHealth\ResultFactory\DefaultResultFactory;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use Cushon\HealthBundle\Message\Result\HealthCheck\Unhealthy;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class DefaultResultFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsAHealthyResult(): void
    {
        $dependency = $this->prophesize(DependencyStatus::class)->reveal();
        $health = $this->prophesize(HealthReport::class);
        $health->isHealthy()->willReturn(true);
        $health->dependencies()->willReturn([$dependency]);

        $factory = new DefaultResultFactory();
        $result = $factory->fromHealth($health->reveal());

        $this->assertInstanceOf(Healthy::class, $result);
        $this->assertSame([$dependency], iterator_to_array($result->dependencies()));
    }

    public function testItReturnsAnUnhealthyResult(): void
    {
        $dependency = $this->prophesize(DependencyStatus::class)->reveal();
        $health = $this->prophesize(HealthReport::class);
        $health->isHealthy()->willReturn(false);
        $health->dependencies()->willReturn([$dependency]);

        $factory = new DefaultResultFactory();
        $result = $factory->fromHealth($health->reveal());

        $this->assertInstanceOf(Unhealthy::class, $result);
        $this->assertSame([$dependency], iterator_to_array($result->dependencies()));
    }
}
