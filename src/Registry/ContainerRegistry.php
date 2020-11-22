<?php declare(strict_types=1);

namespace Mrself\Container\Registry;

use Mrself\Container\Container;

class ContainerRegistry
{
    /**
     * @var array
     */
    static $containers = [];

    /**
     * @param string $namespace
     * @param bool $default
     * @return mixed|Container
     * @throws NotFoundException
     */
    public static function get(string $namespace, $default = false)
    {
        if (!static::has($namespace)) {
            if ($default === false) {
                throw new NotFoundException($namespace);
            }
            return $default;
        }
        return static::$containers[$namespace];
    }

    /**
     * Returns a container by $namespace or creates it and adds to the ContainerRegistry
     * @param string $namespace
     * @return Container
     * @throws InvalidContainerException
     * @throws NotFoundException
     * @throws OverwritingException
     */
    public static function getOrMake(string $namespace)
    {
        $container = static::get($namespace, null);
        return $container ?: static::makeAndAdd($namespace);
    }

    /**
     * Makes a container and adds it to the ContainerRegistry
     * @param string $namespace
     * @return Container
     * @throws InvalidContainerException
     * @throws OverwritingException
     */
    public static function makeAndAdd(string $namespace): Container
    {
        /** @var Container $namespace */
        $container = $namespace::make();
        static::add($namespace, $container);
        return $container;
    }

    /**
     * @param string $namespace
     * @param $container
     * @param bool $overwrite
     * @throws InvalidContainerException
     * @throws OverwritingException
     */
    public static function add(string $namespace, $container, bool $overwrite = false)
    {
        if (!$overwrite && static::has($namespace)) {
            throw new OverwritingException($namespace, $container);
        }
        if (!self::isContainerValid($container)) {
            throw new InvalidContainerException($namespace, $container);
        }
        static::$containers[$namespace] = $container;
    }

    public static function has(string $namespace): bool
    {
        return array_key_exists($namespace, static::$containers);
    }

    public static function setContainers(array $containers)
    {
        self::$containers = $containers;
    }

    public static function getContainers(): array
    {
        return self::$containers;
    }

    public static function reset($skip = [])
    {
        $containers = [];
        $skip = (array) $skip;
        foreach (static::$containers as $namespace => $container) {
            if (in_array($namespace, $skip)) {
                $containers[$namespace] = $container;
            }
        }
        static::$containers = $containers;
    }

    protected static function isContainerValid($container)
    {
        $methods = ['has', 'get', 'getParameter'];
        foreach ($methods as $method) {
            if (!method_exists($container, $method)) {
                return false;
            }
        }
        return true;
    }
}