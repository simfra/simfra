<?php
namespace App;
class Model {
    
    private $_kernel = null; 
    
    public function __construct(\App\Bootstrap $kernel)
    {
        $this->_kernel = $kernel;
    }
    
    
    public function getKernel()
    {
        return $this->_kernel;
    }

}