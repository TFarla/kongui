<?php

namespace Tests\Unit;

use App\Entity\Service;
use App\Service\KongServiceHTTP;
use App\Service\PaginatedResult;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use Illuminate\Support\Collection;
use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Tests\TestCase;

class KongServiceHTTPTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var KongServiceHTTP
     */
    private $service;

    /** @test */
    public function itShouldGetServices(): void
    {
        $result = $this->service->getManyServices();
        $this->assertEquals(new PaginatedResult(collect([]), null), $result);
    }

    /**
     * @dataProvider rawServicesProvider
     * @test
     * @param int $amountOfSerivces
     * @throws \Exception
     * @throws \Http\Client\Exception
     */
    public function itShouldGetServicesWhenThereAreServices(int $amountOfSerivces): void
    {
        $services = $this->make(Service::class, [], $amountOfSerivces);
        if (!($services instanceof Collection)) {
            $services = collect([$services]);
        }

        $this->client->addResponse($this->generateListResponse($services));

        $result = $this->service->getManyServices();
        $lastRequest = $this->client->getLastRequest();
        $this->assertEquals(new PaginatedResult($services, null), $result);
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertJsonHeaders($lastRequest);
    }

    private function generateListResponse(Collection $collection, ?string $next = null): Response
    {
        $data = [
            'data' => $collection->map(\Closure::fromCallable([$this, 'convertServiceToRaw']))->toArray(),
            'next' => $next
        ];

        return new Response(200, [], json_encode($data));
    }

    private function assertJsonHeaders(RequestInterface $request): void
    {
        $this->assertEquals(
            'application/json',
            $request->getHeaderLine('Accept'),
            'Accept header does not provide the specified value'
        );

        if ($request->getBody()->getSize() !== null) {
            $this->assertEquals(
                'application/json',
                $request->getHeaderLine('Content-Type'),
                'Content-Type header does not provide specified value'
            );
        }
    }

    /** @test */
    public function itShouldGetOne(): void
    {
        /** @var Service $service */
        $service = $this->make(Service::class);
        $service->setId('test');

        $raw = $this->convertServiceToRaw($service);
        $response = new Response(200, [], json_encode($raw));
        $this->client->addResponse($response);
        $actual = $this->service->getOneService($service->getId());
        $lastRequest = $this->client->getLastRequest();

        $this->assertEquals($service, $actual);
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals('/services/' . $service->getId(), $lastRequest->getUri()->getPath());
        $this->assertJsonHeaders($lastRequest);
    }

    private function convertServiceToRaw(Service $service): array
    {
        $raw = [];
        foreach ($service->toArray() as $key => $value) {
            $raw[snake_case($key)] = $value;
        }

        return $raw;
    }

    /** @test */
    public function itShouldCreate(): void
    {
        $service = $this->make(Service::class);
        $response = new Response(200, [], json_encode($this->convertServiceToRaw($service)));
        $this->client->addResponse($response);
        $actual = $this->service->createService($service);
        $lastRequest = $this->client->getLastRequest();
        $this->assertEquals($service, $actual);
        $this->assertSame('POST', $lastRequest->getMethod());
        $this->assertSame('/services', $lastRequest->getUri()->getPath());
        $this->assertJsonHeaders($lastRequest);
    }

    /** @test */
    public function itShouldUpdate(): void
    {
        /** @var Service $service */
        $service = $this->make(Service::class);
        $service->setId('test');
        $response = new Response(200, [], json_encode($this->convertServiceToRaw($service)));
        $this->client->addResponse($response);
        $actual = $this->service->putService($service);
        $lastRequest = $this->client->getLastRequest();
        $this->assertEquals($service, $actual);
        $this->assertSame('PUT', $lastRequest->getMethod());
        $this->assertSame('/services/' . $service->getId(), $lastRequest->getUri()->getPath());
        $this->assertJsonHeaders($lastRequest);
    }

    /** @test */
    public function itShouldDelete(): void
    {
        $id = 'test';
        $service = $this->make(Service::class, ['id' => $id]);
        $response = new Response(200, [], json_encode($this->convertServiceToRaw($service)));
        $this->client->addResponse($response);
        $this->assertNull($this->service->deleteService($id));
        $lastRequest = $this->client->getLastRequest();
        $this->assertSame('DELETE', $lastRequest->getMethod());
    }

    public function rawServicesProvider(): array
    {
        return [
            [1],
            [2],
            [50]
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->client = new Client();
        $this->service = new KongServiceHTTP($this->client, $requestFactory);
        $this->client->setDefaultResponse($this->generateListResponse(collect()));
    }
}
