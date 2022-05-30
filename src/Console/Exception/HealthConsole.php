<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Console\Exception;

use Cushon\HealthBundle\Exception\HealthError;
use RuntimeException;
use Throwable;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class HealthConsole extends RuntimeException implements HealthError
{
    /**
     * @param Throwable $exc
     * @return static
     */
    public static function create(Throwable $exc): self
    {
        return new self(
            sprintf('An error occurred running the Health Command: %s', $exc->getMessage()),
            self::HEALTH_ERROR_CODE,
            $exc
        );
    }
}
