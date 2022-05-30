<?php

declare(strict_types=1);

namespace Tests\Unit\ApplicationHealth\HealthReport\Exception;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\Exception\NoDependencyStatuses;
use PHPUnit\Framework\TestCase;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class NoDependencyStatusesTest extends TestCase
{
    public function testItReturnsAZeroidErrorCode(): void
    {
        $noDependencyStatuses = NoDependencyStatuses::create();

        $this->assertSame(0, NoDependencyStatuses::ERROR_CODE);
        $this->assertSame(NoDependencyStatuses::ERROR_CODE, $noDependencyStatuses->getCode());
    }
}
