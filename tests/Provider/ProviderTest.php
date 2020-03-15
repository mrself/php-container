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

}