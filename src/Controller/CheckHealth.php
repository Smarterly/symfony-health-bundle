<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Controller;

use Cushon\HealthBundle\Controller\Exception\HealthController;
use Cushon\HealthBundle\Formatter\Http;
use Cushon\HealthBundle\Message\QueryFactory\QueryFactory;
use Cushon\HealthBundle\QueryBus\HealthCheckQueryBus;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class CheckHealth
{
    private HealthCheckQueryBus $healthCheckQueryBus;
    private QueryFactory $queryFactory;
    private Http $formatter;

    /**
     * @param HealthCheckQueryBus $healthCheckQueryBus
     * @param QueryFactory $queryFactory
     * @param Http $formatter
     */
    public function __construct(HealthCheckQueryBus $healthCheckQueryBus, QueryFactory $queryFactory, Http $formatter)
    {
        $this->healthCheckQueryBus = $healthCheckQueryBus;
        $this->queryFactory = $queryFactory;
        $this->formatter = $formatter;
    }

    /**
     * @return Response
     * @throws HealthController
     */
    public function __invoke(): Response
    {
        try {
            $query = $this->queryFactory->createQuery();
            $result = $this->healthCheckQueryBus->handleHealthCheckQuery($query);

            return $this->formatter->format($result);
        } catch (Throwable $exc) {
            throw HealthController::create($exc);
        }
    }
}
