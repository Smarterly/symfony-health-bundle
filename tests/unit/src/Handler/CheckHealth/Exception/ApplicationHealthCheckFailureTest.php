<?php

declare(strict_types=1);

namespace Tests\Unit\Handler\CheckHealth\Exception;

use Cushon\HealthBundle\ApplicationHealth\Exception\ApplicationHealthError;
use Cushon\HealthBundle\Handler\CheckHealth\Exception\ApplicationHealthCheckFailure;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class ApplicationHealthCheckFailureTest extends TestCase
{
    public function testItReturnsAZeroidErrorCode(): void
    {
        $dependenciesException = new class ('test') extends Exception implements ApplicationHealthError {
        };

        $exception = ApplicationHealthCheckFailure::fromApplicationHealthError(
            $dependenciesException
        );

        $this->assertSame(101, ApplicationHealthCheckFailure::ERROR_CODE);
        $this->assertSame(ApplicationHealthCheckFailure::ERROR_CODE, $exception->getCode());
    }
}
