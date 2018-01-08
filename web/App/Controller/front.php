<?php
namespace App\Controller;

class front extends \Core\Controller
{
    
    public function index()
    {

        // call only proper method in model and return response to main controller
        //mb_strlen();
        //echo "Name: " .  __NAMESPACE__ . "****\n";
        return $this->loadModel("index");
        //(new \App\Model\front)->index()
        //return new \Core\Http\Response\Response($this->_kernel,);
    }

}