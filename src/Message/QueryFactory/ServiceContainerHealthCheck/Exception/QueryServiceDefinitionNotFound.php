<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception;

use Cushon\HealthBundle\Message\QueryFactory\Exception\QueryFactoryException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class QueryServiceDefinitionNotFound extends RuntimeException implements QueryFactoryException
{
    private ContainerInterface $container;
    /**
     * @param ContainerInterface $container
     * @param string $definitionName
     * @return static
     */
    public static function create(ContainerInterface $container, string $definitionName): self
    {
        $exc = new self(sprintf(
            'No service definition for "%s" was found in the container',
            $definitionName
        ));

        $exc->container = $container;

        return $exc;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
