<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Handler\CheckHealth\Exception;

use Cushon\HealthBundle\ApplicationHealth\Exception\ApplicationHealthError;
use RuntimeException;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class ApplicationHealthCheckFailure extends RuntimeException implements CheckHealthHandlerError
{
    /**
     * Use the code of one of the Zeroid Space Sergeant.
     * @see https://en.wikipedia.org/wiki/Terrahawks#Characters
     */
    public const ERROR_CODE = 101;

    /**
     * @param ApplicationHealthError $exc
     * @return self
     */
    public static function fromApplicationHealthError(ApplicationHealthError $exc): self
    {
        return new self(
            sprintf(
                'Unhandled application health dependency check error: %s',
                $exc->getMessage()
            ),
            self::ERROR_CODE,
            $exc
        );
    }
}
