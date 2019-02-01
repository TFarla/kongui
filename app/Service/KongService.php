<?php

namespace App\Service;

use App\Entity\Service;

interface KongService
{
    /**
     * Gets all services
     *
     * @return PaginatedResult
     */
    public function getManyServices(): PaginatedResult;

    /**
     * Gets a single service
     *
     * @param string $id
     * @return Service|null
     */
    public function getOneService(string $id): ?Service;

    /**
     * Create a new service
     *
     * @param Service $service
     * @return Service
     */
    public function createService(Service $service): Service;

    /**
     * Updates a service
     *
     * @param Service $service
     * @return Service
     */
    public function putService(Service $service): Service;

    /**
     * Deletes a services
     * @param string $id
     */
    public function deleteService(string $id): void;
}
