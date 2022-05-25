<?php

declare(strict_types=1);

namespace Tests\Unit\Handler\Logger;

use Cushon\HealthBundle\Encoder\Json\SafeJson;
use Cushon\HealthBundle\Handler\CheckHealth\Exception\CheckHealthHandlerError;
use Cushon\HealthBundle\Handler\CheckHealth\Logger\PsrLogger;
use Cushon\HealthBundle\Message\Query\HealthCheck\DefaultHealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Ergebnis\Json\Printer\Printer;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class PsrLoggerTest extends TestCase
{
    use ProphecyTrait;

    public function testItLogsBeginningAHealthCheck(): void
    {
        $psrLogger = $this->prophesize(LoggerInterface::class);
        $encoder = new SafeJson(new Printer());

        $healthCheckLogger = new PsrLogger(
            $psrLogger->reveal(),
            $encoder
        );

        $healthCheckLogger->begin(new DefaultHealthCheckQuery());

        $psrLogger->log(
            LogLevel::INFO,
            Argument::containingString(DefaultHealthCheckQuery::class),
            PsrLogger::LOG_CONTEXTS
        )->shouldHaveBeenCalled();
    }

    public function testItLogsCompletingAHealthCheck(): void
    {
        $psrLogger = $this->prophesize(LoggerInterface::class);
        $encoder = new SafeJson(new Printer());
        $result = new HealthCheck\Healthy();

        $healthCheckLogger = new PsrLogger(
            $psrLogger->reveal(),
            $encoder
        );

        $healthCheckLogger->complete($result);

        $psrLogger->log(
            LogLevel::INFO,
            Argument::containingString($encoder->encode($result)),
            PsrLogger::LOG_CONTEXTS
        )->shouldHaveBeenCalled();
    }

    public function testItLogsAnError(): void
    {
        $encoder = new SafeJson(new Printer());
        $psrLogger = $this->prophesize(LoggerInterface::class);

        $healthCheckLogger = new PsrLogger(
            $psrLogger->reveal(),
            $encoder
        );

        $exception = new class ('wibble') extends Exception implements CheckHealthHandlerError {
        };

        $healthCheckLogger->error($exception);

        $psrLogger->log(
            LogLevel::ERROR,
            Argument::containingString($exception->getMessage()),
            PsrLogger::LOG_CONTEXTS
        )->shouldHaveBeenCalled();
    }

    public function testTheLogLevelCanBeConfigured(): void
    {
        $encoder = new SafeJson(new Printer());
        $psrLogger = $this->prophesize(LoggerInterface::class);
        $result = new HealthCheck\Healthy();
        $exception = new class ('wibble') extends Exception implements CheckHealthHandlerError {
        };

        $healthCheckLogger = new PsrLogger(
            $psrLogger->reveal(),
            $encoder,
            LogLevel::DEBUG
        );
        $healthCheckLogger->begin(new DefaultHealthCheckQuery());
        $psrLogger->log(
            LogLevel::DEBUG,
            Argument::cetera(),
        )->shouldHaveBeenCalled();

        $healthCheckLogger->complete($result);
        $psrLogger->log(
            LogLevel::DEBUG,
            Argument::cetera(),
        )->shouldHaveBeenCalled();

        $healthCheckLogger->error($exception);

        $psrLogger->log(
            LogLevel::ERROR,
            Argument::cetera(),
        )->shouldHaveBeenCalled();
    }
}
