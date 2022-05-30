<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Handler\CheckHealth;

use Cushon\HealthBundle\ApplicationHealth\Dependencies;
use Cushon\HealthBundle\ApplicationHealth\Exception\ApplicationHealthError;
use Cushon\HealthBundle\Handler\CheckHealth;
use Cushon\HealthBundle\Handler\CheckHealth\Exception\ApplicationHealthCheckFailure;
use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class DefaultHealthCheck implements CheckHealth
{
    private Dependencies $dependencies;
    private Logger $logger;
    private ResultFactory $resultFactory;

    /**
     * @param Dependencies $dependencies
     * @param Logger $logger
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Dependencies $dependencies,
        Logger $logger,
        ResultFactory $resultFactory
    ) {
        $this->dependencies = $dependencies;
        $this->logger = $logger;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(HealthCheckQuery $healthCheck): HealthCheckResult
    {
        $this->logger->begin($healthCheck);
        try {
            $health = $this->dependencies->check();
        } catch (ApplicationHealthError $exc) {
            throw $this->handleApplicationHealthError($exc);
        }

        $result = $this->resultFactory->fromHealth($health);
        $this->logger->complete($result);

        return $result;
    }

    /**
     * @param ApplicationHealthError $exc
     * @return ApplicationHealthCheckFailure
     */
    private function handleApplicationHealthError(ApplicationHealthError $exc): ApplicationHealthCheckFailure
    {
        $failure = ApplicationHealthCheckFailure::fromApplicationHealthError($exc);
        $this->logger->error($failure);

        return $failure;
    }
}
