<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Controller\Exception;

use Cushon\HealthBundle\Exception\HealthError;
use RuntimeException;
use Throwable;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class HealthController extends RuntimeException implements HealthError
{
    /**
     * @param Throwable $exc
     * @return static
     */
    public static function create(Throwable $exc): self
    {
        return new self(
            sprintf('An error occurred during the Health Controller: %s', $exc->getMessage()),
            self::HEALTH_ERROR_CODE,
            $exc
        );
    }
}
