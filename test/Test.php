<?php
    /**
     * Created by PhpStorm.
     * User: polo
     * Date: 27.11.17
     * Time: 19:41
     */

    //include "Controller.php";

    class Test extends Controller
    {
//    public function __construct ()
//    {
//        parent::__construct();
//        echo "Bundle";
//    }
        public function test()
        {
            echo $this->szczekaj();
        }
    }