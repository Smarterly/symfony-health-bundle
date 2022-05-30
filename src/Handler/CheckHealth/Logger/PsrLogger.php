<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Handler\CheckHealth\Logger;

use Cushon\HealthBundle\Encoder\Json\Encoder;
use Cushon\HealthBundle\Handler\CheckHealth\Exception\CheckHealthHandlerError;
use Cushon\HealthBundle\Handler\CheckHealth\Logger;
use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class PsrLogger implements Logger
{
    public const LOG_CONTEXT_HEALTH = 'health';
    public const LOG_CONTEXT_HANDLER = 'handler';

    public const LOG_CONTEXTS = [
        self::LOG_CONTEXT_HEALTH,
        self::LOG_CONTEXT_HANDLER
    ];

    private LoggerInterface $logger;

    private string $logLevel;
    private Encoder $encoder;

    /**
     * @param LoggerInterface $logger
     * @param string $logLevel
     */
    public function __construct(
        LoggerInterface $logger,
        Encoder $encoder,
        string $logLevel = LogLevel::INFO
    ) {
        $this->logger = $logger;
        $this->encoder = $encoder;
        $this->logLevel = $logLevel;
    }


    /**
     * @inheritDoc
     */
    public function begin(HealthCheckQuery $healthCheckQuery): void
    {
        $this->logger->log(
            $this->logLevel,
            sprintf(
                'Beginning application health check (query: %s)',
                get_class($healthCheckQuery)
            ),
            self::LOG_CONTEXTS
        );
    }

    /**
     * @inheritDoc
     */
    public function complete(HealthCheckResult $healthCheckResult): void
    {
        $this->logger->log(
            $this->logLevel,
            sprintf(
                'HealthReport check complete. Result: %s',
                $this->encoder->encode($healthCheckResult)
            ),
            self::LOG_CONTEXTS
        );
    }

    /**
     * @inheritDoc
     */
    public function error(CheckHealthHandlerError $exc): void
    {
        $this->logger->log(
            LogLevel::ERROR,
            sprintf(
                'HealthReport check failed. %s:"%s"',
                get_class($exc),
                $exc->getMessage()
            ),
            self::LOG_CONTEXTS
        );
    }
}
