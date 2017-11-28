<?php
    include "Controller.php";
    include "Model.php";
    include "Debug.php";
    include "Test.php";
    $a = "aaaa";
    new Controller($a);
    new Model();
    //new Debug();
    (new Debug())->test();
    //(new Test())->test();
//echo "asdasd";