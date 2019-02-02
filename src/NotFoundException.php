<?php declare(strict_types=1);

namespace Mrself\Container;

class NotFoundException extends ContainerException
{
    /**
     * @var string
     */
    protected $key;

    public function __construct(string $type, string $key)
    {
        $this->key = $key;
        $type = ucfirst($type);
        parent::__construct("$type by the key $key not found");
    }

    public static function service(string $key)
    {
        return new static('service', $key);
    }

    public static function parameter(string $key)
    {
        return new static('parameter', $key);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}