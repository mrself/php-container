<?php declare(strict_types=1);

namespace Mrself\Container;

class ClassIsNotMakerException extends ContainerException
{
    public function __construct(string $class)
    {
        parent::__construct('The class "' . $class . '" is not maker (not implements OptionableInterface)');
    }
}