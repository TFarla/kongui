<?php

use App\Entity\Service;
use League\FactoryMuffin\Faker\Facade as Faker;

/**
 * @var \League\FactoryMuffin\FactoryMuffin $fm
 */
$fm->define(Service::class)->setDefinitions([
    'id' => Faker::uuid(),
    'name' => Faker::slug(2),
    'url' => Faker::url(),
    'host' => Faker::domainName(),
    'port' => Faker::numberBetween(80, 2 + 000),
    'protocol' => Faker::randomElement(['http', 'https']),
    'connectTimeout' => Faker::numberBetween(10000, 70000),
    'writeTimeout' => Faker::numberBetween(10000, 70000),
    'readTimeout' => Faker::numberBetween(10000, 70000)
]);
