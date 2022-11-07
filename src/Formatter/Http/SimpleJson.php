<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Formatter\Http;

use Cushon\HealthBundle\Encoder\Json\Encoder;
use Cushon\HealthBundle\Formatter\Http;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class SimpleJson implements Http
{
    private Encoder $encoder;
    private int $errorStatus;

    /**
     * @param Encoder $encoder
     */
    public function __construct(Encoder $encoder, int $errorStatus = 500)
    {
        $this->encoder = $encoder;
        $this->errorStatus = $errorStatus;
    }

    /**
     * @inheritDoc
     */
    public function format(HealthCheck $healthCheck): JsonResponse
    {
        return JsonResponse::fromJsonString(
            $this->encode($healthCheck->jsonSerialize()),
            $healthCheck->isHealthy() ? Response::HTTP_OK : $this->errorStatus,
            []
        );
    }

    /**
     * @param mixed $data
     * @return string
     */
    private function encode(mixed $data): string
    {
        return $this->encoder->encode($data);
    }
}
