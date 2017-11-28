<?php
namespace Core;

class Model
{
    
    private $kernel = null;
    
    public function __construct(\Core\Bootstrap $kernel)
    {
        $this->kernel = $kernel;
    }
    
    
    public function getKernel()
    {
        return $this->kernel;
    }

    public function render(string $template)
    {
        //$this->kernel->getBundle("Config");
        $this->getKernel()->getTpl()->fetch($template);
    }

    public function getService($service_name)
    {
        return $this->getKernel()->getService($service_name);
    }



}