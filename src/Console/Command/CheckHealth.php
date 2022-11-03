<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Console\Command;

use Cushon\HealthBundle\Console\Exception\HealthConsole;
use Cushon\HealthBundle\Console\Factory\ResultFormatterFactory;
use Cushon\HealthBundle\Formatter\Console;
use Cushon\HealthBundle\Message\QueryFactory\QueryFactory;
use Cushon\HealthBundle\QueryBus\HealthCheckQueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
#[AsCommand(name: 'cushon:health', description: 'Console command to initiate an bundle health check')]
final class CheckHealth extends Command
{
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
        try {
            $query = $this->queryFactory->createQuery();
            $result = $this->healthCheckQueryBus->handleHealthCheckQuery($query);
            $formatter = $this->createFormatter($input, $output);
            $formatter->format($result);
        } catch (Throwable $e) {
            throw HealthConsole::create($e);
        }

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
