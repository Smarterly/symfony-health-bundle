<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Exception;

use Cushon\HealthBundle\Console\Exception\HealthConsole;
use Cushon\HealthBundle\Exception\HealthError;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class HealthConsoleTest extends TestCase
{
    public function testItUsesThePreviousException(): void
    {
        $msg = 'Unable to do something';
        $previous = new RuntimeException($msg);

        $exc = HealthConsole::create($previous);

        $this->assertStringContainsString($msg, $exc->getMessage());
        $this->assertSame(HealthError::HEALTH_ERROR_CODE, $exc->getCode());
        $this->assertSame($previous, $exc->getPrevious());
    }
}
