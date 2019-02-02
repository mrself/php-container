<?php declare(strict_types=1);

namespace Mrself\Options;

use Mrself\Container\ContainerException;

class InvalidContainerException extends ContainerException
{
    /**
     * @var mixed
     */
    protected $container;

    public function __construct($container)
    {
        $class = get_class($container);
        parent::__construct("Container of class '$class' does not match ContainerInterface (it may not implement interface but only methods)'");
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }
}