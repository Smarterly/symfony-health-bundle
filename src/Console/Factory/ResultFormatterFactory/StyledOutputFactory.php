<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Console\Factory\ResultFormatterFactory;

use Cushon\HealthBundle\Console\Factory\ResultFormatterFactory;
use Cushon\HealthBundle\Formatter\Console\StyledOutput;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\ApplicationStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class StyledOutputFactory implements ResultFormatterFactory
{
    private ApplicationStatus $applicationStatusFormatter;
    private Dependency $dependencyFormatter;

    /**
     * @param ApplicationStatus $applicationStatusFormatter
     * @param Dependency $dependencyFormatter
     */
    public function __construct(
        ApplicationStatus $applicationStatusFormatter,
        Dependency $dependencyFormatter
    ) {
        $this->applicationStatusFormatter = $applicationStatusFormatter;
        $this->dependencyFormatter = $dependencyFormatter;
    }

    /**
     * @inheritDoc
     */
    public function fromIO(InputInterface $input, OutputInterface $output): StyledOutput
    {
        return new StyledOutput(
            new SymfonyStyle($input, $output),
            $this->applicationStatusFormatter,
            $this->dependencyFormatter
        );
    }
}
