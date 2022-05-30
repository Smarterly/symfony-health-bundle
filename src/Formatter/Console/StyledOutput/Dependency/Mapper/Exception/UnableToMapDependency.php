<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\Exception;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper;
use InvalidArgumentException;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class UnableToMapDependency extends InvalidArgumentException
{
    /**
     * @param Mapper $dependencyMapper
     * @param DependencyStatus $dependencyStatus
     * @return static
     */
    public static function create(
        Mapper $dependencyMapper,
        DependencyStatus $dependencyStatus
    ): self {
        return new self(sprintf(
            'Mapper of type %s is unable to map dependency status of type %s',
            get_class($dependencyMapper),
            get_class($dependencyStatus)
        ));
    }
}
