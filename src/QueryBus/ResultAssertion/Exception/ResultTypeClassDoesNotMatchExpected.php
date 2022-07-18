<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus\ResultAssertion\Exception;

use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\QueryBus\Exception\QueryBusError;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class ResultTypeClassDoesNotMatchExpected extends InvalidArgumentException implements AssertionError
{
    /**
     * @param string $classOrInterface
     * @return static
     */
    public static function create(string $classOrInterface): self
    {
        return new self(sprintf(
            'The Result class or interface to test for at runtime must implement %s, received %s',
            HealthCheck::class,
            $classOrInterface
        ));
    }
}
