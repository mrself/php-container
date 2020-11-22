<?php declare(strict_types=1);

namespace Mrself\Container\Tests\Provider;

use Mrself\Container\Container;
use Mrself\Container\Registry\ContainerRegistry;
use Mrself\Container\ServiceProvider;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    public function testRegisterNewContainerWithNamespace()
    {
        $provider = new class extends ServiceProvider {
            protected function getContainer(): Container
            {
                return TestContainer::make();
            }

            protected function getNamespace(): string
            {
                return 'namespace';
            }
        };
        $provider->register();
        $expectedContainer = ContainerRegistry::get('namespace');
        $this->assertInstanceOf(TestContainer::class, $expectedContainer);
    }

    public function testItDoesNotRegisterContainerIfSuchContainerExists()
    {
        $container = TestContainer::make();
        $container->prop = 1;
        ContainerRegistry::add('namespace', $container);

        $provider = new class extends ServiceProvider {
            protected function getContainer(): Container
            {
                return TestContainer::make();
            }

            protected function getNamespace(): string
            {
                return 'namespace';
            }
        };
        $provider->register();
        $expectedContainer = ContainerRegistry::get('namespace');
        $this->assertEquals(1, $expectedContainer->prop);
    }

    public function testItRegistersDependentProviders()
    {
        $provider = new class extends ServiceProvider {
            protected function getContainer(): Container
            {
                return TestContainer::make();
            }

            protected function getNamespace(): string
            {
                return 'namespace';
            }

            protected function getDependentProviders(): array
            {
                return [TestProvider::class];
            }
        };
        $provider->register();
        $expectedContainer = ContainerRegistry::get('namespace2');
        $this->assertInstanceOf(TestContainer::class, $expectedContainer);
    }

    public function testItBootsDependentProviders()
    {
        $provider = new class extends ServiceProvider {
            protected function getContainer(): Container
            {
                return TestContainer::make();
            }

            protected function getNamespace(): string
            {
                return 'namespace';
            }

            protected function getDependentProviders(): array
            {
                return [TestProvider::class];
            }
        };
        $provider->register();
        $provider->boot();
        $container = ContainerRegistry::get('namespace2');
        $this->assertTrue($container->getParameter('isBooted', null));
    }

    public function testContainerMethodReturnsContainer()
    {
        $provider = new class extends ServiceProvider {
            protected function getContainer(): Container
            {
                return TestContainer::make();
            }

            protected function getNamespace(): string
            {
                return 'namespace';
            }
        };
        $provider->register();
        $container = $provider->container();
        $this->assertInstanceOf(TestContainer::class, $container);
    }

    public function testContainerMethodReturnsContainerIfContainerExistedBeforeRegister()
    {
        $provider = new class extends ServiceProvider {
            protected function getContainer(): Container
            {
                return TestContainer::make();
            }

            protected function getNamespace(): string
            {
                return 'namespace';
            }
        };

        $container = TestContainer::make();
        $container->setParameter('property', 'value');
        ContainerRegistry::add('namespace', $container);

        $provider->register();
        $container = $provider->container();

        $this->assertInstanceOf(TestContainer::class, $container);
        $this->assertEquals('value', $container->getParameter('property'));
    }

    protected function setUp()
    {
        parent::setUp();
        ContainerRegistry::reset();
    }
}

class TestContainer extends Container {
    public $prop = 0;
}

class TestProvider extends ServiceProvider {

    protected function getContainer(): Container
    {
        return TestContainer::make();
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