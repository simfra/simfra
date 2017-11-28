<?php
namespace Core;
use lib\Core\Bundle;
use Smarty;

class View extends Bundle
{
    private $template = null;
    public $templateDir = __DIR__ . "/../../templates";
    public $compileDir =  __DIR__ . "/../../cache/templates";

    public function __construct()
    {
        $this->template =new Smarty;
        $this->template->setTemplateDir($this->templateDir)->setCompileDir($this->compileDir);
        Smarty::muteExpectedErrors();
    }
    
    public function assign($key, $value)
    {
        $this->template->assign($key, $value);
    }
    
    
    public function fetch($template, $params = "")
    {
        return $this->template->fetch($template, $params);
    }
    
    public function get_template_vars()
    {
        return $this->template->getTemplateVars();
    }
    
    public function register_object()
    {
        //return $this->_template->assign_by_ref();
    }
}