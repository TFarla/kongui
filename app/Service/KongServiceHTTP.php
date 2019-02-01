<?php

namespace App\Service;

use App\Entity\Service;
use App\Helpers\ArrayHelper;
use Http\Client\HttpClient;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestFactoryInterface;

class KongServiceHTTP implements KongService
{
    /**
     * @var HttpClient
     */
    private $client;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /**
     * KongServiceHTTP constructor.
     * @param HttpClient $client
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(HttpClient $client, RequestFactoryInterface $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Gets all services
     *
     * @return PaginatedResult
     */
    public function getManyServices(): PaginatedResult
    {
        $result = new PaginatedResult(collect([]), null);

        $request = $this->requestFactory->createRequest('GET', '/services')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json');

        $response = $this->client->sendRequest($request);

        $body = json_decode($response->getBody(), true);
        $services = collect();
        foreach ($body['data'] as $rawService) {
            $services->push($this->convertToService($rawService));
        }

        $result->setData($services);

        return $result;
    }

    private function convertToService(array $raw): Service
    {
        $service = new Service();
        foreach ($raw as $key => $value) {
            $method = 'set' . camel_case($key);
            if (method_exists($service, $method)) {
                $service->$method($value);
            }
        }

        return $service;
    }

    /**
     * Gets a single service
     *
     * @param string $id
     * @return Service|null
     */
    public function getOneService(string $id): ?Service
    {
        $request = $this->requestFactory->createRequest('GET', '/services/' . $id)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json');

        $response = $this->client->sendRequest($request);
        $raw = json_decode($response->getBody(), true);

        return $this->convertToService($raw);
    }

    /**
     * Create a new service
     *
     * @param Service $service
     * @return Service
     */
    public function createService(Service $service): Service
    {
        return $this->upsertService($service, 'POST', '/services');
    }

    /**
     * @param Service $service
     * @param string $method
     * @param string $relativePath
     * @return Service
     */
    private function upsertService(Service $service, string $method, string $relativePath): Service
    {
        $body = ArrayHelper::convertKeysToSnakeCase($service->toArray());
        $request = $this->requestFactory->createRequest($method, $relativePath)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json')
            ->withBody(Stream::create(json_encode($body)));

        $response = $this->client->sendRequest($request);

        return $this->convertToService(json_decode($response->getBody(), true));
    }

    /**
     * Updates a service
     *
     * @param Service $service
     * @return Service
     */
    public function putService(Service $service): Service
    {
        $method = 'PUT';
        $relativePath = '/services/' . $service->getId();

        return $this->upsertService($service, $method, $relativePath);
    }

    /**
     * Deletes a services
     * @param string $id
     */
    public function deleteService(string $id): void
    {
        $request = $this->requestFactory->createRequest('DELETE', "/services/$id");
        $this->client->sendRequest($request);
    }
}
