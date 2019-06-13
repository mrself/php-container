<?php declare(strict_types=1);

namespace Mrself\Container\Tests\Container;

use Mrself\Container\Container;
use Mrself\Container\Registry\ContainerRegistry;
use PHPUnit\Framework\TestCase;

class FallbackContainerTest extends TestCase
{
    public function testFallbackContainerAsString()
    {
        $fallbackContainer = Container::make();
        $fallbackContainer->set('service', 'value');
        ContainerRegistry::add('My', $fallbackContainer);
        $container = Container::make([
            'fallbackContainer' => 'My'
        ]);

        $actual = $container->get('service');
        $this->assertEquals('value', $actual);
    }

    public function testFallbackContainerAsObject()
    {
        $fallbackContainer = Container::make();
        $fallbackContainer->set('service', 'value');
        ContainerRegistry::add('My', $fallbackContainer);
        $container = Container::make([
            'fallbackContainer' => $fallbackContainer
        ]);

        $actual = $container->get('service');
        $this->assertEquals('value', $actual);
    }

    protected function setUp()
    {
        parent::setUp();
        ContainerRegistry::reset();
    }
}