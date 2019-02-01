<?php

namespace App\Service;

use App\Entity\Service;

/**
 * Class KongServiceMemory
 * @package App\Service
 */
class KongServiceMemory implements KongService
{

    /** @var Service[] */
    private $services = [];

    /**
     * Gets all services
     *
     * @return PaginatedResult
     */
    public function getManyServices(): PaginatedResult
    {
        return new PaginatedResult(collect($this->services), null);
    }

    /**
     * Gets a single service
     *
     * @param string $id
     * @return Service|null
     */
    public function getOneService(string $id): ?Service
    {
        foreach ($this->services as $service) {
            if ($this->matches($id, $service)) {
                return $service;
            }
        }

        return null;
    }

    /**
     * @param string $id
     * @param Service $service
     * @return bool
     */
    private function matches(string $id, Service $service): bool
    {
        return in_array($id, [$service->getId(), $service->getName()], true);
    }

    /**
     * Create a new service
     *
     * @param Service $service
     * @return Service
     * @throws \Exception
     */
    public function createService(Service $service): Service
    {
        $newService = clone $service;
        $newService->setId(uniqid('service', true));
        $newService->setCreatedAt(new \DateTime());
        $this->services[] = $newService;
        return $newService;
    }

    /**
     * Updates a service
     *
     * @param Service $service
     * @return Service
     * @throws \Exception
     */
    public function putService(Service $service): Service
    {
        $updatedService = clone $service;
        $id = $updatedService->getId();
        foreach ($this->services as $key => $oldService) {
            if ($this->matches($id, $oldService)) {
                $updatedService->setUpdatedAt(new \DateTime());
                $this->services[$key] = $updatedService;
            }
        }

        return $updatedService;
    }

    /**
     * Deletes a services
     * @param string $id
     */
    public function deleteService(string $id): void
    {
        foreach ($this->services as $key => $service) {
            if ($this->matches($id, $service)) {
                unset($this->services[$key]);
            }
        }
    }
}
