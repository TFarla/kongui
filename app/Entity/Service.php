<?php

namespace App\Entity;

/**
 * Class Service
 * @package App\Entity
 */
class Service
{
    private static $fillable = [
        'url',
        'name',
        'host',
        'port',
        'protocol',
        'path',
        'connectTimeout',
        'writeTimeout',
        'readTimeout'
    ];
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $url;
    /**
     * @var string|null
     */
    private $host;
    /**
     * @var int|null
     */
    private $port;
    /**
     * @var string|null
     */
    private $protocol;
    /**
     * @var string|null
     */
    private $path;
    /**
     * @var string|null
     */
    private $name;
    /** @var \DateTime|null */
    private $createdAt;
    /** @var \DateTime|null */
    private $updatedAt;
    /**
     * @var int|null
     */
    private $connectTimeout;
    /**
     * @var int|null
     */
    private $readTimeout;
    /**
     * @var int|null
     */
    private $writeTimeout;

    /**
     * @return array
     */
    public static function getFillable(): array
    {
        return static::$fillable;
    }

    public function fill(array $fields): void
    {
        foreach ($fields as $name => $value) {
            if (!in_array($name, static::$fillable)) {
                throw new \InvalidArgumentException("Field $name is not fillable for a service");
            }

            $method = "set$name";
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'url' => $this->getUrl(),
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'protocol' => $this->getProtocol(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'connectTimeout' => $this->getConnectTimeout(),
            'writeTimeout' => $this->getWriteTimeout(),
            'readTimeout' => $this->getReadTimeout()
        ];
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string|null $host
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param int|null $port
     */
    public function setPort(?int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string|null
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * @param string|null $protocol
     */
    public function setProtocol(?string $protocol): void
    {
        $this->protocol = $protocol;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int|null
     */
    public function getConnectTimeout(): ?int
    {
        return $this->connectTimeout;
    }

    /**
     * @param int|null $connectTimeout
     */
    public function setConnectTimeout(?int $connectTimeout): void
    {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * @return int|null
     */
    public function getWriteTimeout(): ?int
    {
        return $this->writeTimeout;
    }

    /**
     * @param int|null $writeTimeout
     */
    public function setWriteTimeout(?int $writeTimeout): void
    {
        $this->writeTimeout = $writeTimeout;
    }

    /**
     * @return int|null
     */
    public function getReadTimeout(): ?int
    {
        return $this->readTimeout;
    }

    /**
     * @param int|null $readTimeout
     */
    public function setReadTimeout(?int $readTimeout): void
    {
        $this->readTimeout = $readTimeout;
    }
}
