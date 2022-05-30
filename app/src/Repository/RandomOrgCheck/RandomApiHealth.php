<?php

declare(strict_types=1);

namespace App\Repository\RandomOrgCheck;

use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\HealthDependencyRepository;
use App\Repository\Exception\ResponseNotOk;
use App\Repository\Exception\SymfonyHttpException;
use App\Repository\Exception\UnknownApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

final class RandomApiHealth implements HealthDependencyRepository
{
    public const DEFAULT_MIN = 1;

    private HttpClientInterface $httpClient;
    private int $min;

    /**
     * @param HttpClientInterface $randomClient
     * @param int $min
     */
    public function __construct(HttpClientInterface $randomClient, int $min = self::DEFAULT_MIN)
    {
        $this->httpClient = $randomClient;
        $this->min = $min;
    }

    /**
     * @inheritDoc
     */
    public function fetchRandomNumber(): int
    {
        try {
            $response = $this->httpClient->request(
                Request::METHOD_GET,
                '/integers/',
                [
                    'query' => $this->getQueryParams(),
                ]
            );
        } catch (ExceptionInterface $e) {
            throw SymfonyHttpException::fromPrevious($e);
        } catch (Throwable $e) {
            throw UnknownApiException::create($e);
        }

        return $this->parseResponse($response);
    }

    /**
     * @param ResponseInterface $response
     * @return int
     */
    private function parseResponse(ResponseInterface $response): int
    {
        $statusCode = $response->getStatusCode();
        if (Response::HTTP_OK !== $statusCode) {
            throw ResponseNotOk::fromResponseCode($statusCode);
        }

        return (int) $response->getContent();
    }

    /**
     * @return array
     */
    private function getQueryParams(): array
    {
        # https://www.random.org/integers/?num=10&min=1&max=6&col=1&base=10&format=plain&rnd=new
        return [
            'num' => 1,
            'min' => $this->min,
            'max' => 6,
            'col' => 1,
            'base' => 10,
            'format' => 'plain',
            'rnd' => 'new'
        ];
    }
}
