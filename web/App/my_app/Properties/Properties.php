<?php

namespace App\my_app\Properties;


class Properties
{
    private $aa;
    public function __construct(\Core\Debug\Debug $a)
    {
        echo "properties " . $a;
    }
}