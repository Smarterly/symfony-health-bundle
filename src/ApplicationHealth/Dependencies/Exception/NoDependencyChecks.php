<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth\Dependencies\Exception;

use DomainException;

final class NoDependencyChecks extends DomainException
{
    /**
     * Use the code of one of the Zeroid 55.
     * @see https://en.wikipedia.org/wiki/Terrahawks#Characters
     */
    public const ERROR_CODE = 55;

    /**
     * @return static
     */
    public static function create(): self
    {
        return new self(
            'A health check must have at least one dependency checker',
            self::ERROR_CODE
        );
    }
}
