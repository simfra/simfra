<?php
namespace Core;

use Core\Exception\FatalException;
use Smarty;

class View extends Bundle
{
    private $template = null;
    public $templateDir = ROOT_DIR . "templates/";//__DIR__ . "/../../templates";
    public $compileDir =  "";//__DIR__ . "/../../cache/templates";

    public function __constru6ct()
    {
        //$this->getKernel()->getApplicationPath();
        //echo "<pre>";
        //print_r($this);//debug_backtrace());
        //echo "</pre>";
        //$this->template = new Smarty;
        //$this->template->setTemplateDir($this->templateDir)->setCompileDir($this->compileDir);
        //Smarty::muteExpectedErrors();
    }

    private function getTemplate()
    {
        if ($this->template === null) {
            $this->template = new Smarty;
            if (!file_exists($this->compileDir)) {
                if (!mkdir($this->compileDir, 0777, true)) {
                    die('Failed to create folder: ' . $this->compileDir);
                }
            }
            $this->template->setTemplateDir($this->templateDir)->setCompileDir($this->compileDir);
            Smarty::muteExpectedErrors();
        }
        return $this->template;
    }
    
    public function assign($key, $value)
    {
        $this->getTemplate()->assign($key, $value);
    }
    
    
    public function fetch($template, $params = "")
    {
        try {
            return $this->getTemplate()->fetch($template, $params);
        } catch (\Exception $e) {
            throw new FatalException("View", $e->getMessage());
        }
    }
    
    public function get_template_vars()
    {
        return $this->getTemplate()->getTemplateVars();
    }
    
    public function register_object()
    {
        //return $this->_template->assign_by_ref();
    }
}