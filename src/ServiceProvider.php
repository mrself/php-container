<?php declare(strict_types=1);

namespace Mrself\Container;

use Mrself\Container\Registry\ContainerRegistry;

abstract class ServiceProvider
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ServiceProvider[]
     */
    protected $dependencies = [];

    public function register()
    {
        $namespace = $this->getNamespace();
        if (ContainerRegistry::has($namespace)) {
            return;
        }

        $this->container = $this->getContainer();
        ContainerRegistry::add($namespace, $this->container);

        $this->makeDependencies();
        $this->registerDependencies();
        $this->selfRegister();
    }

    public function container()
    {
        return $this->container ?: ContainerRegistry::get($this->getNamespace());
    }

    public function boot()
    {
        $this->bootDependencies();
        $this->selfBoot();
    }

    public function registerAndBoot()
    {
        $this->register();
        $this->boot();
    }

    protected function makeDependencies()
    {
        foreach ($this->getDependentProviders() as $provider) {
            $this->dependencies[] = $provider::make();
        }
    }

    protected function getDependentProviders(): array
    {
        return [];
    }

    abstract protected function getContainer(): Container;

    abstract protected function getNamespace(): string;

    public static function make()
    {
        return new static();
    }

    protected function registerDependencies()
    {
        foreach ($this->dependencies as $dependency) {
            $dependency->register();
        }
    }

    protected function bootDependencies()
    {
        foreach ($this->dependencies as $dependency) {
            $dependency->boot();
        }
    }

    protected function selfBoot()
    {
    }

    protected function selfRegister()
    {
    }
}