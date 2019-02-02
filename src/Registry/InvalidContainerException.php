<?php declare(strict_types=1);

namespace Mrself\Container\Registry;

use Mrself\Container\ContainerException;

class InvalidContainerException extends ContainerException
{
    /**
     * @var mixed
     */
    protected $container;

    /**
     * @var string
     */
    protected $key;

    public function __construct(string $key, $container)
    {
        $this->key = $key;
        $this->container = $container;
        if (is_scalar($container)) {
            $type = gettype($container);
        } else {
            $type = get_class($container);
        }
        parent::__construct("Container of class/type '$type' with the key '$key' does not match ContainerInterface (it may not implement interface but only methods)'");
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}