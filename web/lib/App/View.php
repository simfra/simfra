<?php
namespace App;
use Smarty;
class View {
    private $_template = null;
    
    public function __construct(){
        $this->_template = new Smarty;
        $this->_template->setTemplateDir(__DIR__ . "/../../templates")->setCompileDir(__DIR__ . "/../../cache/templates");
        Smarty::muteExpectedErrors();        
    }
    
    public function assign($key, $value)
    {
        $this->_template->assign($key, $value);
    }
    
    
    public function fetch($template, $params="")
    {
        return $this->_template->fetch($template, $params);
    }
    
    public function get_template_vars()
    {
        return $this->_template->getTemplateVars();
    }
}