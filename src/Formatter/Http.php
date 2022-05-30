<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter;

use Cushon\HealthBundle\Message\Result\HealthCheck;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface Http
{
    /**
     * @param HealthCheck $healthCheck
     * @return Response
     */
    public function format(HealthCheck $healthCheck): Response;
}
