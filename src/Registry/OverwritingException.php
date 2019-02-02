<?php declare(strict_types=1);

namespace Mrself\Container\Registry;

use Mrself\Container\ContainerException;

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

    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;

        parent::__construct("Trying to overwrite the container by the key '$key'. To force overwriting call the method with \$overwrite = true.");
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