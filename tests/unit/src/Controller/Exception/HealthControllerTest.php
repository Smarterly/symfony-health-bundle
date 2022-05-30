<?php
declare(strict_types=1);

namespace Tests\Unit\Controller\Exception;

use Cushon\HealthBundle\Controller\Exception\HealthController;
use Cushon\HealthBundle\Exception\HealthError;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class HealthControllerTest extends TestCase
{
    public function testItUsesThePreviousException(): void
    {
        $msg = 'Unable to do something';
        $previous = new RuntimeException($msg);

        $exc = HealthController::create($previous);

        $this->assertStringContainsString($msg, $exc->getMessage());
        $this->assertSame(HealthError::HEALTH_ERROR_CODE, $exc->getCode());
        $this->assertSame($previous, $exc->getPrevious());
    }
}
