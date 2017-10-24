<?php
namespace Core\Objects;
use Core\Interfaces\ArrayMethod;
class App_Array implements ArrayMethod {
    private $values = array();
    public function __call($name, $aa)
    {
     //       
    // return "assdas";
    }
    
    function __invoke()
    {
        return $this;
    }
    

    function __debugInfo()
    {
        return $this->values;
    }
    
    
    public function getAll()
    {
        return $this->values;
    }
    function __toString()
    {
        return print_r($this->values, true);
    }
        
    
    public function __construct($initial_values="")
    {
        if($initial_values!=""){
            $this->values = $initial_values;
        }
    }
    
    public function get($variable="")
    {
        if(trim($variable)!="" && isset($this->values[$variable]))
        {
            return $this->values[$variable];
        }
        else{
            $debug = debug_backtrace();
            trigger_error("Undefined object property ($variable) - Line: ". $debug[0]['line'] . " File: " .$debug[0]['file'] ." Function: ". $debug[0]['function'], E_USER_NOTICE);
            return null;
        }
    }
    
    public function set($variable, $value)
    {
        $this->values[$variable] = $value;
    }
    
    public function add($value)
    {
        $this->values[] = $value;
    }
}
