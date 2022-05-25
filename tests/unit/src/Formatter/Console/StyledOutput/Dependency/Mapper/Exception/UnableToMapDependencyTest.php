<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Console\StyledOutput\Dependency\Mapper\Exception;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\Exception\UnableToMapDependency;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class UnableToMapDependencyTest extends TestCase
{
    use ProphecyTrait;

    public function testItContainsTheMapperAndDependencyInTheMessage(): void
    {
        /** @var Mapper $dependencyMapper */
        $dependencyMapper = $this->prophesize(Mapper::class)->reveal();
        /** @var DependencyStatus $dependencyStatus */
        $dependencyStatus = $this->prophesize(DependencyStatus::class)->reveal();

        $exc = UnableToMapDependency::create(
            $dependencyMapper,
            $dependencyStatus
        );

        $this->assertStringContainsString(get_class($dependencyMapper), $exc->getMessage());
        $this->assertStringContainsString(get_class($dependencyStatus), $exc->getMessage());
    }
}
