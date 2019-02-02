<?php declare(strict_types=1);

namespace Mrself\Container\Tests\Registry;

use Mrself\Container\Registry\ContainerRegistry;
use Mrself\Container\Registry\InvalidContainerException;
use Mrself\Container\Registry\NotFoundException;
use Mrself\Container\Registry\OverwritingException;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    /**
     * @var ContainerRegistry
     */
    protected $registry;

    /**
     * @var mixed
     */
    protected $container;

    public function testGet()
    {
        ContainerRegistry::setContainers(['key' => $this->container]);
        $this->assertEquals($this->container, ContainerRegistry::get('key'));
    }

    public function testGetThrowsErrorIfRegistryDoesNotContainerContainerByKey()
    {
        try {
            ContainerRegistry::get('key');
        } catch (NotFoundException $e) {
            $this->assertEquals('key', $e->getKey());
            return;
        }
        $this->assertTrue(false);
    }

    public function testAdd()
    {
        $this->container->has();
        ContainerRegistry::add('key', $this->container);
        $containers = ContainerRegistry::getContainers();
        $this->assertEquals($this->container, $containers['key']);
    }

    public function testAddThrowsExceptionIfContainerIsOfInvalidFormat()
    {
        try {
            ContainerRegistry::add('key', 1);
        } catch (InvalidContainerException $e) {
            $this->assertEquals('key', $e->getKey());
            $this->assertEquals(1, $e->getContainer());
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddThrowsExceptionIfContainerExistsByKeyAndOverwriteIsFalse()
    {
        ContainerRegistry::add('key', $this->container);
        try {
            ContainerRegistry::add('key', $this->container);
        } catch (OverwritingException $e) {
            $this->assertEquals('key', $e->getKey());
            $this->assertEquals($this->container, $e->getContainer());
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddOverwritesExistingContainerIfOverwriteParamIsTrue()
    {
        ContainerRegistry::add('key', $this->container);
        ContainerRegistry::add('key', $this->container, true);
        $this->assertTrue(true);
    }

    public function testHasReturnsTrueIfContainerExists()
    {
        ContainerRegistry::add('key', $this->container);
        $this->assertTrue(ContainerRegistry::has('key'));
    }

    public function testHasReturnsFalseIfContainerIsAbsent()
    {
        $this->assertFalse(ContainerRegistry::has('key'));
    }

    protected function defineContainer()
    {
        $this->container = new class {
            public function get() {}
            public function has() {}
            public function getParameter() {}
        };
    }

    public function setUp()
    {
        $this->defineContainer();
    }

    public function tearDown()
    {
        parent::tearDown();
        ContainerRegistry::reset();
    }
}