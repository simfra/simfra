<?php

namespace lib\Core;

abstract class Bundle
{
    public $container = null;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function getKernel()
    {
        return "lllsdasdas";// $this->kernel;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function defaultConfig($config)
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }
}