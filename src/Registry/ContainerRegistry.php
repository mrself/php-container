<?php declare(strict_types=1);

namespace Mrself\Container\Registry;

use Mrself\Options\InvalidContainerException;

class ContainerRegistry
{
    /**
     * @var array
     */
    static $containers = [];

    /**
     * @param string $namespace
     * @return mixed
     * @throws NotFoundException
     */
    public static function get(string $namespace)
    {
        if (!static::has($namespace)) {
            throw new NotFoundException($namespace);
        }
        return static::$containers[$namespace];
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
            throw new InvalidContainerException($container);
        }
        static::$containers[$namespace] = $container;
    }

    public static function has(string $namespace): bool
    {
        return array_key_exists($namespace, static::$containers);
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