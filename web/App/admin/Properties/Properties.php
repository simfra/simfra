<?php

namespace App\Properties;


class Properties
{
    private $aa;
    public function __construct(\Core\Debug\Debug $a, \App\Bozon $aa)
    {
        echo "properties " . $a;
    }
}