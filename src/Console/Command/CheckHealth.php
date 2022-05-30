<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Console\Command;

use Cushon\HealthBundle\Console\Factory\ResultFormatterFactory;
use Cushon\HealthBundle\Formatter\Console;
use Cushon\HealthBundle\Message\QueryFactory\QueryFactory;
use Cushon\HealthBundle\QueryBus\HealthCheckQueryBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class CheckHealth extends Command
{
    public const DEFAULT_NAME = 'cushon:health';
    public const DEFAULT_DESCRIPTION = 'Console command to initiate an bundle health check';

    protected static $defaultName = self::DEFAULT_NAME;
    protected static $defaultDescription = self::DEFAULT_DESCRIPTION;

    private HealthCheckQueryBus $healthCheckQueryBus;
    private QueryFactory $queryFactory;
    private ResultFormatterFactory $resultFormatterFactory;

    /**
     * @param HealthCheckQueryBus $healthCheckQueryBus
     * @param QueryFactory $queryFactory
     * @param ResultFormatterFactory $resultFormatterFactory
     */
    public function __construct(
        HealthCheckQueryBus $healthCheckQueryBus,
        QueryFactory $queryFactory,
        ResultFormatterFactory $resultFormatterFactory
    ) {
        $this->healthCheckQueryBus = $healthCheckQueryBus;
        $this->queryFactory = $queryFactory;
        $this->resultFormatterFactory = $resultFormatterFactory;

        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = $this->queryFactory->createQuery();
        $result = $this->healthCheckQueryBus->handleHealthCheckQuery($query);

        $formatter = $this->createFormatter($input, $output);

        $formatter->format($result);

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Console
     */
    private function createFormatter(InputInterface $input, OutputInterface $output): Console
    {
        return $this->resultFormatterFactory->fromIO($input, $output);
    }
}
