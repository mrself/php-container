<?php declare(strict_types=1);

namespace Mrself\Container;

class OverwritingException extends ContainerException
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct(string $type, string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;

        parent::__construct("Trying to overwrite the $type by the key '$key'. To force overwriting call the method with \$overwrite = true.");
    }

    public static function service(string $key, $value)
    {
        return new static('service', $key, $value);
    }

    public static function parameter(string $key, $value)
    {
        return new static('parameter', $key, $value);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}