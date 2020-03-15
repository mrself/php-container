<?php declare(strict_types=1);

namespace Mrself\Container;

use Mrself\Container\Registry\ContainerRegistry;

abstract class ServiceProvider
{
    public function register()
    {
        $namespace = $this->getNamespace();
        if (ContainerRegistry::has($namespace)) {
            return;
        }

        ContainerRegistry::add($namespace, $this->getContainer());

        foreach ($this->getDependentProviders() as $provider) {
            $provider::make()->register();
        }
    }

    public function boot()
    {

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
}