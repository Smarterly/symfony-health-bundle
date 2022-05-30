<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\ApplicationHealth;

use Cushon\HealthBundle\ApplicationHealth\Exception\ApplicationHealthError;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface Dependencies
{
    /**
     * An application-specific service that determines if the application is "healthy",
     * which is an arbitrary decision based on many unique factors (ie if an API is down, a DB not readable, etc).
     * @return HealthReport
     * @throws ApplicationHealthError should there be an error in checking dependencies
     */
    public function check(): HealthReport;
}
