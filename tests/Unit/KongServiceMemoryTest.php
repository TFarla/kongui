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
        $this->assertEquals([], $this->service->getAll());
    }

    /** @test */
    public function itShouldBeAbleToAdd(): void
    {
        $newService = new Service();
        $addedService = $this->service->create($newService);
        $this->assertEquals([$addedService], $this->service->getAll());
    }

    /** @test */
    public function itShouldBeAbleToUpdate(): void
    {
        $service = new Service();
        $newService = $this->service->create($service);
        $newService->setName('something else');
        $updatedService = $this->service->put($newService);

        $this->assertNotEquals($newService, $updatedService);
        $this->assertEquals([$updatedService], $this->service->getAll());
    }

    /** @test */
    public function itShouldBeAbleToRemove(): void
    {
        $service = new Service();
        $newService = $this->service->create($service);
        $this->service->delete($newService->getId());
        $this->assertNull($this->service->getOne($newService->getId()));
    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KongService();
    }
}
