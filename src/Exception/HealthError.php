<?php
declare(strict_types=1);

namespace Cushon\HealthBundle\Exception;

use Throwable;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface HealthError extends Throwable
{
    public const HEALTH_ERROR_CODE = 101;
}
