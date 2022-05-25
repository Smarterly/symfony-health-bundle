<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus\ResultAssertion\Exception;

use Cushon\HealthBundle\QueryBus\Exception\QueryBusError;
use InvalidArgumentException;

final class ResultClassOrInterfaceNotFound extends InvalidArgumentException implements AssertionError
{
    /**
     * @param string $classOrInterface
     * @return static
     */
    public static function fromString(string $classOrInterface): self
    {
        return new self(sprintf('Could not find a class or interface called %s', $classOrInterface));
    }
}
