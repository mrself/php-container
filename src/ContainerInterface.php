<?php declare(strict_types=1);

namespace Mrself\Container;

interface ContainerInterface
{
    public function get(string $name, $default = false);

    public function set(string $name, $service, bool $overwrite = false);

    public function has(string $name): bool;

    public function fallbackHas(string $name): bool;

    public function getParameter(string $name, $default = false);

    public function setParameter(string $name, $param, bool $overwrite = false);

    public static function make(array $options = []);
}