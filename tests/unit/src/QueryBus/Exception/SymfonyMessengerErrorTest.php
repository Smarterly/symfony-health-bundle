<?php

declare(strict_types=1);

namespace Tests\Unit\QueryBus\Exception;

use Cushon\HealthBundle\QueryBus\Exception\SymfonyMessengerError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Exception\LogicException;

final class SymfonyMessengerErrorTest extends TestCase
{
    public function testItReturnsAZeroidErrorCode(): void
    {
        $symfonyException = new LogicException('Error');

        $symfonyMessengerError = SymfonyMessengerError::fromMessengerException($symfonyException);

        $this->assertSame(18, SymfonyMessengerError::ERROR_CODE);
        $this->assertSame(SymfonyMessengerError::ERROR_CODE, $symfonyMessengerError->getCode());
    }
}
