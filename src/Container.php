<?php declare(strict_types=1);

namespace Mrself\Container;

use Mrself\Container\Registry\ContainerRegistry;
use Mrself\Options\Annotation\Option;
use Mrself\Options\WithOptionsTrait;

class Container
{
    use WithOptionsTrait {
        make as parentMake;
    }

    /**
     * @Option()
     * @var ContainerInterface[]|string[]
     */
    protected $fallbackContainers = [];

    /**
     * @Option()
     * @var array
     */
	protected $services = [];

    /**
     * @Option()
     * @var array
     */
	protected $params = [];

	protected function __construct()
    {
    }

    /**
     * @param string $key
     * @param bool $default
     * @return bool|mixed
     * @throws Registry\NotFoundException
     */
    public function get(string $key, $default = false)
    {
        if (!$this->has($key)) {
            if ($default === false) {
                throw NotFoundException::service($key);
            }
            return $default;
        }
        return $this->internalGet($key, $default);
	}

    /**
     * @param string $key
     * @param $default
     * @return mixed
     * @throws Registry\NotFoundException
     */
    protected function internalGet(string $key, $default)
	{
        if ($this->ownHas($key)) {
            return $this->services[$key];
        }

        foreach ($this->getFallbackContainers() as $container) {
            if ($container->has($key)) {
                return $container->get($key);
            }
        }


        return $default;
	}

    /**
     * @param string $key
     * @param $service
     * @param bool $overwrite
     * @throws Registry\NotFoundException
     */
    public function set(string $key, $service, bool $overwrite = false)
    {
        if (!$overwrite && $this->has($key)) {
            throw OverwritingException::service($key, $service);
        }
        $this->services[$key] = $service;
	}

    /**
     * @param string $key
     * @return bool
     * @throws Registry\NotFoundException
     */
    public function has(string $key): bool
    {
        $result = $this->ownHas($key);
        if ($result) {
            return true;
        }

        return $this->fallbackHas($key);
	}

    /**
     * @param string $key
     * @return bool
     * @throws Registry\NotFoundException
     */
    public function fallbackHas(string $key): bool
	{
        foreach ($this->getFallbackContainers() as $container) {
            if ($container->has($key)) {
                return true;
            }
        }

        return false;
	}

    public function ownHas(string $key): bool
    {
        return array_key_exists($key, $this->services);
	}

    public function getParameter(string $key, $default = false)
    {
        if (!$this->hasParameter($key)) {
            if ($default === false) {
                throw NotFoundException::parameter($key);
            }
            return $default;
        }
        return $this->params[$key];
    }

    public function setParameter(string $key, $param, bool $overwrite = false)
    {
        if (!$overwrite && $this->hasParameter($key)) {
            throw OverwritingException::parameter($key, $param);
        }
        $this->params[$key] = $param;
    }

    public function hasParameter(string $key): bool
    {
        return array_key_exists($key, $this->params);
    }

    public function setServices(array $services)
    {
        $this->services = $services;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function setParameters(array $parameters)
    {
        $this->params = $parameters;
    }

    public function getParameters(): array
    {
        return $this->params;
    }

    public static function with(array $services = [], array $params = [])
    {
        return static::create(compact('services', 'params'));
    }

    public static function make(array $options = [])
    {
        return static::parentMake($options);
    }

    public static function create(array $options = []): self
    {
        return static::parentMake($options);
    }

    /**
     * @return ContainerInterface[]
     * @throws Registry\NotFoundException
     */
    protected function getFallbackContainers(): array
    {
        $result = [];
        foreach ($this->fallbackContainers as $container) {
            if (is_string($container)) {
                $container = ContainerRegistry::get($container, null);
                if (is_null($container)) {
                    continue;
                }
            }
            $result[] = $container;
        }
        return $result;
    }
}