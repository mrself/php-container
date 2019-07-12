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
            'fallbackContainers' => ['My']
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
            'fallbackContainers' => [$fallbackContainer]
        ]);

        $actual = $container->get('service');
        $this->assertEquals('value', $actual);
    }

    /**
     * @expectedException \Mrself\Container\NotFoundException
     */
    public function testItDoesNotUseContainerIfItDoesNotExist()
    {
        $container = Container::make([
            'fallbackContainers' => ['My']
        ]);

        $container->get('service');
    }

    protected function setUp()
    {
        parent::setUp();
        ContainerRegistry::reset();
    }
}