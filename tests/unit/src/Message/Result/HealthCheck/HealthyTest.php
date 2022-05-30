<?php

declare(strict_types=1);

namespace Tests\Unit\Message\Result\HealthCheck;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function Safe\json_encode;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class HealthyTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsHealthy(): void
    {
        $dependency = new SimpleStatus('foo', true);
        $healthy = new Healthy($dependency);

        $this->assertTrue($healthy->isHealthy());
    }

    public function testItReturnsTheDependencies(): void
    {
        $dependency1 = new SimpleStatus('foo', true);
        $dependency2 = new SimpleStatus('bar', true);

        $healthy = new Healthy($dependency1, $dependency2);
        $this->assertSame(
            [$dependency1, $dependency2],
            iterator_to_array($healthy())
        );
    }

    public function testItIsJsonSerializable(): void
    {
        $dependency = new SimpleStatus('foo', true);
        $unhealthy = new Healthy($dependency);

        $this->assertSame(
            '{"status":"healthy","dependencies":[{"name":"foo","healthy":true,"info":null}]}',
            json_encode($unhealthy->jsonSerialize())
        );
    }
}
