<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth\HealthReport\Exception;

use InvalidArgumentException;

final class NoDependencyStatuses extends InvalidArgumentException
{
    /**
     * Use the code of one of the Zeroid Sergeant Major Zero.
     * @see https://en.wikipedia.org/wiki/Terrahawks#Characters
     */
    public const ERROR_CODE = 0;

    /**
     * @return static
     */
    public static function create(): self
    {
        return new self(
            'A Health Report must have at least one dependency report',
            self::ERROR_CODE
        );
    }
}
