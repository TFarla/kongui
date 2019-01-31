<?php

namespace Tests;

use League\FactoryMuffin\Exceptions\DefinitionNotFoundException;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var FactoryMuffin
     */
    protected static $factory;

    /**
     * @throws \League\FactoryMuffin\Exceptions\DirectoryNotFoundException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$factory = new FactoryMuffin();
        static::$factory->loadFactories(__DIR__ . DIRECTORY_SEPARATOR . 'Factory');
        Faker::instance()->setLocale('en_EN');
    }

    /**
     * @param string $modelOrEntity
     * @param array $overrides
     * @param int $amount
     * @return \Illuminate\Support\Collection|mixed|object
     * @throws \Exception
     */
    public function make(string $modelOrEntity, array $overrides = [], int $amount = 1)
    {
        try {
            if ($amount === 1) {
                return static::$factory->instance($modelOrEntity, $overrides);
            }

            $entities = collect();
            while ($entities->count() < $amount) {
                $entity = static::$factory->instance($modelOrEntity, $overrides);
                $entities->push($entity);
            }

            return $entities;
        } catch (DefinitionNotFoundException $e) {
            if ($amount === 1) {
                return factory($modelOrEntity)->make($overrides);
            }

            return factory($modelOrEntity, $amount)->make($overrides);
        }

        throw new \Exception("No factory configured for model or entity with name $modelOrEntity");
    }
}
