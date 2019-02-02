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

    public function get(string $key, $default = false)
    {
        if (!$this->has($key)) {
            if ($default === false) {
                throw NotFoundException::service($key);
            }
            return $default;
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
        return static::make(compact('services', 'params'));
    }

    public static function make(array $options)
    {
        $self = new static();
        $self->setServices($options['services']);
        $self->setParameters($options['params']);
        return $self;
    }
}