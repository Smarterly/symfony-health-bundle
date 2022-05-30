<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Console\StyledOutput\Dependency\DependencyTable;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\Mapper\SimpleMapper;
use Cushon\HealthBundle\Formatter\Console\StyledOutput\Dependency\DependencyTable\DependencyRowFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\TableCell;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class DependencyRowFactoryTest extends TestCase
{
    public function testItReturnsAHealthyDependencyRow(): void
    {
        $dependencyRowFactory = new DependencyRowFactory(new SimpleMapper());
        $simpleStatus = new SimpleStatus('Some service', true, 'all good');

        $row = iterator_to_array($dependencyRowFactory->createDependencyRow($simpleStatus));

        $this->assertArrayHasKey(SimpleMapper::KEY_NAME, $row);
        $this->assertArrayHasKey(SimpleMapper::KEY_HEALTH, $row);
        $this->assertArrayHasKey(SimpleMapper::KEY_INFO, $row);
        /** @var TableCell $name */
        $name = $row[SimpleMapper::KEY_NAME];
        $options = $name->getStyle()->getOptions();
        $this->assertSame(DependencyRowFactory::DEFAULT_COLOUR_HEALTHY, $options['fg']);
    }
}
