<?php declare(strict_types=1);

namespace Mrself\Container\Tests\Provider;

use Mrself\Container\Container;
use Mrself\Container\Registry\ContainerRegistry;
use Mrself\Container\ServiceProvider;
use PHPUnit\Framework\TestCase;

class FallbackContainerTest extends TestCase
{
    public function testFallbackContainersAreSet()
    {
        $provider = new class extends ServiceProvider {
            protected function getContainer(): Container
            {
                return FallbackTestContainer::make();
            }

            protected function getNamespace(): string
            {
                return 'namespace';
            }

            protected function getDependentProviders(): array
            {
                return [FallbackTestProvider::class];
            }

            protected function getFallbackContainers(): array
            {
                return ['namespace2'];
            }
        };

        $provider->registerAndBoot();

        ContainerRegistry::get('namespace2')->set('service', 'value');
        $value = ContainerRegistry::get('namespace')->get('service');
        $this->assertEquals('value', $value);
    }

    protected function setUp()
    {
        ContainerRegistry::reset();
    }
}

class FallbackTestContainer extends Container {
    public $prop = 0;
}

class FallbackTestContainer2 extends Container {
    public $prop = 1;
}

class FallbackTestProvider extends ServiceProvider {

    protected function getContainer(): Container
    {
        return FallbackTestContainer2::make();
    }

    protected function getNamespace(): string
    {
        return 'namespace2';
    }

    public function boot()
    {
        $this->container->setParameter('isBooted', true);
    }
}