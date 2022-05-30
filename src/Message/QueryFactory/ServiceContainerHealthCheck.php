<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\QueryFactory;

use Cushon\HealthBundle\Message\Query\HealthCheck;
use Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception\IncorrectQueryType;
use Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception\QueryServiceDefinitionNotFound;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class ServiceContainerHealthCheck implements QueryFactory
{
    private ContainerInterface $container;
    private string $definitionName;

    /**
     * @param ContainerInterface $container
     * @param string $definitionName
     */
    public function __construct(
        ContainerInterface $container,
        string $definitionName = HealthCheck::class
    ) {
        $this->container = $container;
        $this->definitionName = $definitionName;
    }


    /**
     * @inheritDoc
     */
    public function createQuery(): HealthCheck
    {
        if (!$this->container->has($this->definitionName)) {
            throw QueryServiceDefinitionNotFound::create($this->container, $this->definitionName);
        }

        /** @var object $query */
        $query =  $this->container->get(HealthCheck::class);
        $this->assertHealthCheck($query);

        /** @var HealthCheck $query */
        return $query;
    }

    /**
     * @param object $query
     * @return void
     * @psalm-assert CheckHealth $query
     */
    private function assertHealthCheck(object $query): void
    {
        if (!$query instanceof HealthCheck) {
            throw IncorrectQueryType::create(
                HealthCheck::class,
                $query
            );
        }
    }
}
