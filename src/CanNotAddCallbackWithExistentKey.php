<?php declare(strict_types=1);

namespace Mrself\Container;

class CanNotAddCallbackWithExistentKey extends ContainerException
{
    public function __construct(string $key)
    {
        $message = 'The service with the key "' . $key . '" already exists';
        parent::__construct($message);
    }
}