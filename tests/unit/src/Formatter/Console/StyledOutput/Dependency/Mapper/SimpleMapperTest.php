<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Console\StyledOutput\Dependency\Mapper;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\Exception\UnableToMapDependency;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\SimpleMapper;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class SimpleMapperTest extends TestCase
{
    use ProphecyTrait;

    public function testItThrowsAnExceptionIfItCannotMapTheDependency(): void
    {
        /** @var DependencyStatus $dependencyStatus */
        $dependencyStatus = $this->prophesize(DependencyStatus::class)->reveal();
        $dependencyMapper = new SimpleMapper();
        $this->expectExceptionObject(UnableToMapDependency::create($dependencyMapper, $dependencyStatus));

        $dependencyMapper->mapDependencyStatus($dependencyStatus);
    }

    public function testItReturnsHeaders(): void
    {
        $name = 'foo';
        $health = 'bar';
        $info = 'baz';
        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(true);

        $dependencyMapper = new SimpleMapper($name, $health, $info);

        $headers = iterator_to_array(
            $dependencyMapper->getHealthCheckHeaders($healthCheck->reveal())
        );
        $this->assertArrayHasKey(SimpleMapper::KEY_NAME, $headers);
        $this->assertArrayHasKey(SimpleMapper::KEY_HEALTH, $headers);
        $this->assertArrayHasKey(SimpleMapper::KEY_INFO, $headers);

        $this->assertSame($name, $headers[SimpleMapper::KEY_NAME]);
        $this->assertSame($health, $headers[SimpleMapper::KEY_HEALTH]);
        $this->assertSame($info, $headers[SimpleMapper::KEY_INFO]);
    }

    public function testItReturnsAnUnhealthyRow(): void
    {
        $dependencyMapper = new SimpleMapper();
        $serviceName = 'Some Vendor API';

        $unhealthyStatus = new SimpleStatus($serviceName, false);

        $row = iterator_to_array($dependencyMapper->mapDependencyStatus($unhealthyStatus));
        $this->assertSame($serviceName, $row[SimpleMapper::KEY_NAME]);
        $this->assertSame(Dependency::STATUS_UNHEALTHY, $row[SimpleMapper::KEY_HEALTH]);
        $this->assertSame('', $row[SimpleMapper::KEY_INFO]);
    }

    public function testItReturnsAHealthyRow(): void
    {
        $dependencyMapper = new SimpleMapper();
        $serviceName = 'Database';
        $info = 'Able to read and write';
        $unhealthyStatus = new SimpleStatus($serviceName, true, $info);

        $row = iterator_to_array($dependencyMapper->mapDependencyStatus($unhealthyStatus));
        $this->assertSame($serviceName, $row[SimpleMapper::KEY_NAME]);
        $this->assertSame(Dependency::STATUS_HEALTHY, $row[SimpleMapper::KEY_HEALTH]);
        $this->assertSame($info, $row[SimpleMapper::KEY_INFO]);
    }
}
