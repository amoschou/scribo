<?php

namespace AMoschou\Scribo\App\Classes;

class Binder
{
    private $name;
    private $routePrefix;

    public function __construct($name)
    {
        $this->name = $name;
        $this->routePrefix = config("scribo.binders.{$name}.route_prefix");
    }

    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    public function getName()
    {
        retunr $this->name;
    }
}
