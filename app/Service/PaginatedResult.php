<?php

namespace App\Service;

use Illuminate\Support\Collection;

class PaginatedResult
{
    /**
     * @var Collection
     */
    private $data;

    /**
     * @var string|null
     */
    private $next;

    /**
     * PaginatedResult constructor.
     * @param Collection $data
     * @param string|null $next
     */
    public function __construct(Collection $data, ?string $next)
    {
        $this->data = $data;
        $this->next = $next;
    }

    /**
     * @return Collection
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    /**
     * @param Collection $data
     */
    public function setData(Collection $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getNext(): ?string
    {
        return $this->next;
    }

    /**
     * @param string $next
     */
    public function setNext(string $next): void
    {
        $this->next = $next;
    }
}
