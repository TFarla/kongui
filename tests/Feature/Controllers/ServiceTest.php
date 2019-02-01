<?php

namespace Tests\Feature\TFarla\Kongui\Controllers;

use App\Entity\Service;
use App\Service\KongService;
use App\Service\KongServiceMemory;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    /**
     * @var KongServiceMemory
     */
    private $kongService;

    /**
     * @test
     * @dataProvider routeProvider
     * @param string $routeName
     * @param array $routeParams
     */
    public function itShouldRequireTheUserToBeAuthenticated(string $routeName, array $routeParams = []): void
    {
        $this->expectException(AuthenticationException::class);
        $route = route($routeName, $routeParams);
        $this->get($route);
    }

    /**
     * @test
     * @dataProvider servicesProvider
     * @param Service[] $services
     * @throws \Exception
     */
    public function itShouldListServices(\Closure $setupServices): void
    {
        /**
         * @var Collection $services
         */
        $services = $setupServices();
        foreach ($services as $service) {
            $this->kongService->createService($service);
        }

        $this->signIn();
        $response = $this->get(route('services.index'));

        if ($services->count() === 0) {
            $response->assertSeeText('No services have been created');
        }

        $services->each(function (Service $service) use ($response) {
            $response->assertSeeText($service->getName());
        });
    }

    public function signIn(): void
    {
        $user = $this->make(User::class);
        $this->be($user);
    }

    /**
     * @test
     */
    public function itShouldShowCreateForm(): void
    {
        $this->signIn();
        $response = $this->get(route('services.create'));
        $response->assertOk();
        $response->assertSeeText('Create service');
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCreateAService(): void
    {
        /**
         * @var Service $service
         */
        $service = static::$factory->instance(Service::class);

        $this->signIn();

        $response = $this->post(route('services.store'), $service->toArray());

        $response->assertRedirect();
        $this->assertCount(1, $this->kongService->getManyServices()->getData()->toArray());
    }

    /**
     * @test
     * @dataProvider invalidFieldsProvider
     * @param array $data
     */
    public function itShouldValidateServicesDuringCreation(array $data): void
    {
        /**
         * @var Service $service
         */
        $service = $this->make(Service::class);
        $this->expectException(ValidationException::class);
        $this->signIn();
        $postData = array_merge($service->toArray(), $data);
        $this->post(route('services.store'), $postData);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCreateAServiceWithoutUrl(): void
    {
        /**
         * @var Service $service
         */
        $service = $this->make(Service::class, [
            'url' => null,
            'protocol' => 'http',
            'host' => 'example.com',
            'port' => 80
        ]);

        $this->signIn();
        $this->post(route('services.store'), $service->toArray())->assertRedirect();
    }

    /**
     * @test
     */
    public function itShouldThrowNotFoundWhenServiceNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->signIn();
        $this->get(route('services.show', ['service' => 'none']));
    }


    /**
     * @dataProvider invalidFieldsProvider
     * @test
     */
    public function itShouldValidateUpdate(array $invalidFields): void
    {
        $this->expectException(ValidationException::class);
        $this->signIn();
        $service = $this->kongService->createService(
            $this->make(Service::class)
        );

        $newFields = array_merge($service->toArray(), $invalidFields);
        $this->put(route('services.update', ['service' => $service->getId()]), $newFields);
    }

    /**
     * @test
     */
    public function itShouldThrowANotFoundExceptionOnUpdate(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->signIn();
        /** @var Service $service */
        $service = $this->make(Service::class);
        $this->put(route('services.update', ['service' => 'test']), $service->toArray());
    }

    /**
     * @test
     */
    public function itShouldUpdate(): void
    {
        $this->signIn();
        $oldService = $this->kongService->createService(
            $this->make(Service::class)
        );

        $id = $oldService->getId();
        /** @var Service $service */
        $service = $this->make(Service::class);
        $service->setId($id);
        $this->put(route('services.update', ['service' => $id]), $service->toArray());
        $updatedService = $this->kongService->getOneService($id);
        $service->setUpdatedAt($updatedService->getUpdatedAt());
        $service->setCreatedAt($updatedService->getCreatedAt());
        $this->assertEquals(
            $service,
            $updatedService
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function itShouldShowService(): void
    {
        /** @var Service $service */
        $service = $this->make(Service::class);
        $service = $this->kongService->createService($service);

        $this->signIn();
        $response = $this->get(route('services.show', ['service' => $service->getId()]));
        $response->assertOk();
        $response->assertSeeText($service->getName());
    }

    /**
     * @return array
     */
    public function invalidFieldsProvider(): array
    {
        return [
            [
                [
                    'name' => ''
                ]
            ],
            [
                [
                    'url' => '',
                    'host' => null
                ]
            ],
            [
                [
                    'port' => -1
                ]
            ],
            [
                [
                    'host' => 1
                ]
            ],
            [
                [
                    'protocol' => 1
                ]
            ],
            [
                [
                    'url' => null,
                    'port' => null,
                    'host' => null,
                    'protocol' => null
                ]
            ],
            [[
                'connectTimeout' => -1
            ]],
            [
                [
                    'writeTimeout' => -1
                ]
            ],
            [
                [
                    'readTimeout' => -1
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function servicesProvider(): array
    {
        return [
            [
                function () {
                    return collect();
                }
            ],
            [
                function () {
                    return $this->make(Service::class, [], 10);
                }
            ]
        ];
    }

    /**
     * @test
     */
    public function itShouldShowServiceToEdit(): void
    {
        $this->signIn();
        $service = $this->kongService->createService($this->make(Service::class));
        $this->get(route('services.edit', ['service' => $service->getId()]))
            ->assertSeeText($service->getName());
    }

    /**
     * @test
     */
    public function itShouldThrowNotFoundIfServiceToEditIsNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->signIn();
        $this->get(route('services.edit', ['service' => 'test']));
    }

    public function routeProvider(): array
    {
        return [
            ['services.index'],
            ['services.create'],
            ['services.store'],
            ['services.show', ['test']],
            ['services.edit', ['test']],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->kongService = new KongServiceMemory();
        $this->app->singleton(KongService::class, function () {
            return $this->kongService;
        });
    }
}
