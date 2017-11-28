<?php
    /**
     * Created by PhpStorm.
     * User: polo
     * Date: 27.11.17
     * Time: 19:41
     */

    //include "Controller.php";

class Debug extends Controller
{
//    public function __construct()
//    {
////        parent::__construct();
//        echo "<br/>Bundle: " . $this->szczekaj() ." <br/>";
//    }
    public function test()
    {
        echo "***";
        echo $this->szczekaj();
    }
}