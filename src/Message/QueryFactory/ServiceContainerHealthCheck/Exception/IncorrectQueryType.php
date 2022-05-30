<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception;

use Cushon\HealthBundle\Message\QueryFactory\Exception\QueryFactoryException;
use InvalidArgumentException;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class IncorrectQueryType extends InvalidArgumentException implements QueryFactoryException
{
    /**
     * @param string $expected
     * @param object $actual
     * @return static
     */
    public static function create(string $expected, object $actual): self
    {
        return new self(sprintf(
            'Expected an instance of %s, but the query is of type %s',
            $expected,
            get_class($actual)
        ));
    }
}
