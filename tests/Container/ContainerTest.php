<?php declare(strict_types=1);

namespace Mrself\Container\Tests\Container;

use Mrself\Container\Container;
use Mrself\Container\NotFoundException;
use Mrself\Container\OverwritingException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function testItRetrievesServiceByKey()
    {
        $this->container->setServices(['key' => 1]);
        $this->assertEquals(1, $this->container->get('key'));
    }

    public function testGetThrowsErrorIfServiceDoesNotExistByProvidedKey()
    {
        try {
            $this->container->get('key');
        } catch (NotFoundException $e) {
            $this->assertEquals('key', $e->getKey());
            return;
        }
        $this->assertTrue(false);
    }

    public function testItSetsServiceByKey()
    {
        $this->container->set('key', 1);
        $services = $this->container->getServices();
        $this->assertEquals(1, $services['key']);
    }

    public function testSetThrowsErrorIfContainerHasServiceByProvidedKeyAndIsNotOverwrite()
    {
        $this->container->set('key', 1);
        try {
            $this->container->set('key', 1);
        } catch (OverwritingException $e) {
            $this->assertEquals('key', $e->getKey());
            $this->assertEquals(1, $e->getValue());
            return;
        }
        $this->assertTrue(false);
    }

    public function testHasReturnsTrueIfServiceExists()
    {
        $this->container->setServices(['key' => 1]);
        $this->assertTrue($this->container->has('key'));
    }

    public function testHasReturnsFalseIfServiceDoesNotExist()
    {
        $this->assertFalse($this->container->has('key'));
    }

    public function testItRetrievesParameterByKey()
    {
        $this->container->setParameters(['key' => 1]);
        $this->assertEquals(1, $this->container->getParameter('key'));
    }

    public function testGetParameterThrowsErrorIfServiceDoesNotExistByProvidedKey()
    {
        try {
            $this->container->getParameter('key');
        } catch (NotFoundException $e) {
            $this->assertEquals('key', $e->getKey());
            return;
        }
        $this->assertTrue(false);
    }

    public function testItSetsParameterByKey()
    {
        $this->container->setParameter('key', 1);
        $parameters = $this->container->getParameters();
        $this->assertEquals(1, $parameters['key']);
    }

    public function testSetParameterThrowsErrorIfContainerHasParameterByProvidedKeyAndIsNotOverwrite()
    {
        $this->container->setParameter('key', 1);
        try {
            $this->container->setParameter('key', 1);
        } catch (OverwritingException $e) {
            $this->assertEquals('key', $e->getKey());
            $this->assertEquals(1, $e->getValue());
            return;
        }
        $this->assertTrue(false);
    }

    public function testHasParameterReturnsTrueIfParameterExists()
    {
        $this->container->setParameters(['key' => 1]);
        $this->assertTrue($this->container->hasParameter('key'));
    }

    public function testHasParameterReturnsFalseIfParameterDoesNotExist()
    {
        $this->assertFalse($this->container->has('key'));
    }

    public function setUp()
    {
        $this->container = new Container();
    }
}