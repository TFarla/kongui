<?php

namespace Test\Unit\TFarla\Kongui\Service;

use App\Entity\Service;
use App\Service\KongServiceMemory as KongService;
use Tests\TestCase;

class KongServiceMemoryTest extends TestCase
{
    /** @var KongService */
    private $service;

    /** @test */
    public function itShouldGetAll(): void
    {
        $this->assertEquals([], $this->service->getManyServices()->getData()->toArray());
    }

    /** @test */
    public function itShouldBeAbleToAdd(): void
    {
        $newService = new Service();
        $addedService = $this->service->createService($newService);
        $this->assertEquals([$addedService], $this->service->getManyServices()->getData()->toArray());
    }

    /** @test */
    public function itShouldBeAbleToUpdate(): void
    {
        $service = new Service();
        $newService = $this->service->createService($service);
        $newService->setName('something else');
        $updatedService = $this->service->putService($newService);

        $this->assertNotEquals($newService, $updatedService);
        $this->assertEquals([$updatedService], $this->service->getManyServices()->getData()->toArray());
    }

    /** @test */
    public function itShouldBeAbleToRemove(): void
    {
        $service = new Service();
        $newService = $this->service->createService($service);
        $this->service->deleteService($newService->getId());
        $this->assertNull($this->service->getOneService($newService->getId()));
    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KongService();
    }
}
