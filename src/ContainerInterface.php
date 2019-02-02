<?php declare(strict_types=1);

namespace Mrself\Container;

interface ContainerInterface
{
    public function get(string $name);

    public function has(string $name);

    public function getParameter(string $name);
}