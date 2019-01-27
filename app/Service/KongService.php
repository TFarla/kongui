<?php

namespace App\Service;

use App\Entity\Service;

interface KongService
{
    /**
     * Gets all services
     *
     * @return Service[]
     */
    public function getAll(): array;

    /**
     * Gets a single service
     *
     * @param string $id
     * @return Service|null
     */
    public function getOne(string $id): ?Service;

    /**
     * Create a new service
     *
     * @param Service $service
     * @return Service
     */
    public function create(Service $service): Service;

    /**
     * Updates a service
     *
     * @param Service $service
     * @return Service
     */
    public function put(Service $service): Service;

    /**
     * Deletes a services
     * @param string $id
     */
    public function delete(string $id): void;
}
