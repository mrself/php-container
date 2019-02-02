<?php declare(strict_types=1);

namespace Mrself\Container;

class Container
{
    /**
     * @var array
     */
	protected $services = [];

    /**
     * @var array
     */
	protected $params = [];

	// @todo add default value param
    public function get(string $key)
    {
        if (!$this->has($key)) {
            throw NotFoundException::service($key);
        }
        return $this->services[$key];
	}

    public function set(string $key, $service, bool $overwrite = false)
    {
        if (!$overwrite && $this->has($key)) {
            throw OverwritingException::service($key, $service);
        }
        $this->services[$key] = $service;
	}

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->services);
	}

    public function getParameter(string $key)
    {
        if (!$this->hasParameter($key)) {
            throw NotFoundException::parameter($key);
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
}