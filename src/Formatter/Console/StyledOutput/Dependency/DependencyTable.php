<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency;

use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable\DependencyRowFactory;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable\TableHeaderFactory;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class DependencyTable implements Dependency
{
    private TableHeaderFactory $tableHeaderFactory;
    private DependencyRowFactory $dependencyRowFactory;

    public function __construct(
        TableHeaderFactory $tableHeaderFactory,
        DependencyRowFactory $dependencyRowFactory
    ) {
        $this->tableHeaderFactory = $tableHeaderFactory;
        $this->dependencyRowFactory = $dependencyRowFactory;
    }

    /**
     * @inheritDoc
     */
    public function format(HealthCheck $healthCheck, SymfonyStyle $styler): void
    {
        $table = $styler->createTable();

        $table->setHeaderTitle('Dependencies');
        $this->addDependencyRows($healthCheck, $table);
        $this->addHeaders($healthCheck, $table);
        $table->render();
    }

    /**
     * @param HealthCheck $healthCheck
     * @param Table $table
     * @return void
     */
    private function addHeaders(HealthCheck $healthCheck, Table $table): void
    {
        $table->setHeaders(iterator_to_array($this->tableHeaderFactory->createHeaders($healthCheck)));
    }

    /**
     * @param HealthCheck $healthCheck
     * @param Table $table
     * @return void
     */
    private function addDependencyRows(HealthCheck $healthCheck, Table $table): void
    {
        foreach ($healthCheck->dependencies() as $dependency) {
            $table->addRow(iterator_to_array(
                $this->dependencyRowFactory->createDependencyRow($dependency)
            ));
        }
    }
}
