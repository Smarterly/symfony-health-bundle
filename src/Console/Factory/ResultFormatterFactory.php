<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Console\Factory;

use Cushon\HealthBundle\Formatter\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface ResultFormatterFactory
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Console
     */
    public function fromIO(InputInterface $input, OutputInterface $output): Console;
}
