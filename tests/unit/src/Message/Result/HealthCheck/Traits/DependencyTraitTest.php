<?php

declare(strict_types=1);

namespace Tests\Unit\Message\Result\HealthCheck\Traits;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Message\Result\Traits\DependencyTrait;
use Ds\Set;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class DependencyTraitTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @return void
     */
    public function testItReturnsDependencies(): void
    {
        $dependency = $this->prophesize(DependencyStatus::class)->reveal();

        $class = new class ($dependency) {
            use DependencyTrait;

            public function __construct(DependencyStatus $dependency)
            {
                $this->dependencies = new Set([$dependency]);
            }
        };

        $dependencies = iterator_to_array($class->dependencies());

        $this->assertSame([$dependency], $dependencies);
    }
}
