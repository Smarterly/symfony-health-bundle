<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Message\QueryFactory\ServiceContainerHealthCheck\Exception;

use Cushon\HealthBundle\Message\QueryFactory\Exception\QueryFactoryException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
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
        return new self(
            $container,
            sprintf(
                'No service definition for "%s" was found in the container',
                $definitionName
            )
        );
    }

    /**
     * @param ContainerInterface $container
     * @param string $message
     */
    private function __construct(ContainerInterface $container, string $message)
    {
        $this->container = $container;
        parent::__construct($message);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
