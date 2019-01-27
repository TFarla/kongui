<?php

namespace Tests\Feature\TFarla\Kongui\Controllers;

use App\Entity\Service;
use App\Service\KongService;
use App\Service\KongServiceMemory;
use App\User;
use Illuminate\Auth\AuthenticationException;
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
    public function itShouldListServices(Service ...$services): void
    {
        foreach ($services as $service) {
            $this->kongService->create($service);
        }

        $this->signIn();
        $response = $this->get(route('services.index'));

        if (count($services) === 0) {
            $response->assertSeeText('No services have been created');
        }

        foreach ($services as $service) {
            $response->assertSeeText($service->getName());
        }
    }

    public function signIn(): void
    {
        $user = factory(User::class)->make();
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
        $this->signIn();
        $response = $this->post(route('services.store'), [
            'name' => 'something'
        ]);
        $response->assertRedirect();
        $this->assertCount(1, $this->kongService->getAll());
    }

    /**
     * @test
     * @dataProvider invalidFieldsProvider
     * @param array $data
     */
    public function itShouldValidateServicesDuringCreation(array $data): void
    {
        $this->expectException(ValidationException::class);
        $this->signIn();
        $this->post(route('services.store'), $data);
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
     * @test
     * @throws \Exception
     */
    public function itShouldShowService(): void
    {
        $service = new Service();
        $service->setName('test');
        $service = $this->kongService->create($service);

        $this->signIn();
        $response = $this->get(route('services.show', ['service' => $service->getId()]));
        $response->assertOk();
        $response->assertSeeText($service->getName());
    }

    public function invalidFieldsProvider()
    {
        $validFields = [
            'name' => 'test'
        ];

        return [
            [
                []
            ],
            [
                array_except($validFields, 'name')
            ],
            [
                ['name' => '']
            ],
            [
                ['name' => 's']
            ]
        ];
    }

    /**
     * @return array
     */
    public function servicesProvider(): array
    {
        $service = new Service();
        $service->setName('test');

        return [
            [],
            [$service]
        ];
    }

    public function routeProvider(): array
    {
        return [
            ['services.index'],
            ['services.create'],
            ['services.store'],
            ['services.show', ['test']]
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
