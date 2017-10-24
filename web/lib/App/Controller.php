<?php
namespace App;
use \App\Bootstrap;
use \Core\Exception\FatalException;
class Controller {
    private $_kernel = null;
    protected  $tpl = null;

    function __construct(\App\Bootstrap $kernel)
    {
        $this->kernel = $kernel;
    }
    
    
    
    public function getKernel()
    {
        return $this->_kernel;
    }
    
    
    
    public function loadModel($name)
    {
        if(method_exists(new \App\Model\front($this->kernel), $name)) {
           return (new \App\Model\front($this->kernel))->{$name}();
        }
        else{
            throw new FatalException("Controller", "Unable to load model ($name)");
        }        
        
    }
    
    
    public function callControllerMethod($name)
    {
        if(method_exists($this, $name)) {
            return new \Core\Http\Response\Response($this->kernel,$this->{$name}());
        }
        else{
            throw new FatalException("Controller", "No method exists ($name) in this class");
        }
    }
    
};