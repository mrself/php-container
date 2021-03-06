<?php declare(strict_types=1);

namespace Mrself\Container;

use Mrself\Container\Registry\ContainerRegistry;
use Mrself\Options\Annotation\Option;
use Mrself\Options\OptionableInterface;
use Mrself\Options\OptionsUtil;
use Mrself\Options\WithOptionsTrait;

class Container implements ContainerInterface
{
    use WithOptionsTrait;

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

    /**
     * @var array[]
     */
	protected $callbacks = [];

	protected $callbacksCache = [];

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
        if ($this->hasCallback($key)) {
            $this->addCallback($key);
            return $this->services[$key];
        }

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
    protected function internalGet(string $key, $default = false)
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
        if (!$overwrite && $this->ownHas($key)) {
            throw OverwritingException::service($key, $service);
        }
        $this->services[$key] = $service;
	}

    /**
     * Adds an optionable class (maker) as a service.
     *
     * ```php
     * class Maker implements OptionableInterface {
     * use WithOptionsTrait;
     * }
     * $container->setMaker(Maker::class);
     * ```
     *
     * @param string $class
     * @param bool $shared
     * @param array $params
     * @throws CanNotAddCallbackWithExistentKey
     * @throws ClassIsNotMakerException
     * @see WithOptionsTrait
     * @see OptionableInterface
     */
    public function setMaker(string $class, bool $shared = true, array $params = [])
    {
        if (!OptionsUtil::isClassOptionable($class)) {
            throw new ClassIsNotMakerException($class);
        }

        $this->on($class, function () use ($class, $params) {
            return $class::make($params);
        }, $shared);
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

    public function addFallbackContainer($container)
    {
        $this->fallbackContainers[] = $container;
	}

    /**
     * @param Container[] $containers
     */
    public function addFallbackContainers(array $containers)
    {
        $this->fallbackContainers = array_merge($this->fallbackContainers, $containers);
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
        return array_key_exists($key, $this->services) || $this->hasCallback($key);
	}

    protected function addCallback($id)
    {
        $callback = $this->callbacksCache[$id];

        if ($callback['isSingleton'] && !isset($this->services[$id])) {
            $this->services[$id] = $callback['callback']();
        }
	}

    public function hasCallback($id): bool
    {
        $result = $this->findCallback($id);

        if ($result) {
            $this->callbacksCache[$id] = $result;
        }

        return !!$result;
	}

    protected function findCallback($id)
    {
        $result = array_filter($this->callbacks, function (array $callback) use ($id) {
            return $callback['id'] === $id;
        });
        return reset($result);
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

    public function on(string $serviceId, $callback, bool $isSingleton = true)
    {
        if (isset($this->services[$serviceId])) {
            throw new CanNotAddCallbackWithExistentKey($serviceId);
        }

        $this->callbacks[] = [
            'callback' => $callback,
            'id' => $serviceId,
            'isSingleton' => $isSingleton
        ];
    }

    public static function with(array $services = [], array $params = [])
    {
        return static::create(compact('services', 'params'));
    }

    public static function create(array $options = []): self
    {
        return static::make($options);
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