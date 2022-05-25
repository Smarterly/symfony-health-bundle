<?php

declare(strict_types=1);

namespace Tests\Unit\Message\Result\HealthCheck;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Message\Result\HealthCheck\Unhealthy;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function Safe\json_encode;

final class UnhealthyTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsHealthy(): void
    {
        $dependency = new SimpleStatus('foo', false);
        $unhealthy = new Unhealthy($dependency);

        $this->assertFalse($unhealthy->isHealthy());
    }

    public function testItReturnsTheDependencies(): void
    {
        $dependency1 = new SimpleStatus('foo', false);
        $dependency2 = new SimpleStatus('bar', true);

        $unhealthy = new Unhealthy($dependency1, $dependency2);
        $this->assertSame(
            [$dependency1, $dependency2],
            iterator_to_array($unhealthy())
        );
    }

    public function testItIsJsonSerializable(): void
    {
        $dependency = new SimpleStatus('foo', false);
        $unhealthy = new Unhealthy($dependency);

        $this->assertSame(
            '{"status":"unhealthy","dependencies":[{"name":"foo","healthy":false,"info":null}]}',
            json_encode($unhealthy->jsonSerialize())
        );
    }
}
