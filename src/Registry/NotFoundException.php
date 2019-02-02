<?php declare(strict_types=1);

namespace Mrself\Container\Registry;

use Mrself\Container\ContainerException;

class NotFoundException extends ContainerException
{
    /**
     * @var string
     */
    protected $key;

    public function __construct(string $key)
    {
        $this->key = $key;
        parent::__construct("Container by the key $key not found");
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}