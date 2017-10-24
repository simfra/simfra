<?php
namespace App\Debug;
use Core\Objects\App_Array;
use Core\Objects\App_Object;
use App\Bundle;
class Debug {
    private $_debug = [];
    private $_kernel;
    public $show_buffer = false;
    public function __construct(\App\Bootstrap $kernel)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);     
        $this->_debug = ["notice"=>[], "warning"=>[]];
        $this->_kernel = $kernel;
        set_error_handler(array($this, "ErrorException"));    
        register_shutdown_function([$this, "ShutDown"]);
        //trigger_error("Error Message", E_USER_WARNING);
    }   
    
    public function addDebugError($type, $error)
    {
        $this->_debug[$type][] =$error;        
    }
    
    public function makeDevToolbar($content)
    {
        if($this->_kernel->isBundle("View")) {
            $tpl = $this->_kernel->getBundle("View");
            $tpl->assign("dev",$this->devToolbar());
            $tpl->assign("path_www", URL);
            $tpl->assign("ismobile", $this->_kernel->isMobile);
            $tpl->assign("dev_templates", $this->parseTemplate($tpl->get_template_vars()));
            $toolbar = $tpl->fetch("Debug/toolbar.tpl");
            if($this->show_buffer == true && mb_strlen(ob_get_contents())) {
                $buffer = '<div style="margin-top: 30px;"><h2>Buffered output</h2><div style="display: block;font-family: monospace;padding: 9.5px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color: #f5f5f5;border: 1px solid #ccc;border-radius: 4px;
                      min-height: 200px;max-height: 600px;overflow: auto;">'.ob_get_contents()."</div></div>";
            }
            else{
                $buffer = "";
            }
            ob_end_clean();
            (strpos($content, "<!DOCTYPE html>")=== false) ? $c = "<!DOCTYPE html>".$c : "";
            (strpos($content, "<body")=== false) ? $body = "<body>" : $body = "";
            if(strpos($content, "</head>")!== false) {
                $content = str_replace("</head>","<link type=\"text/css\" href=\"".URL."/css/toolbar.css\" rel=\"stylesheet\" /></head>$body", $content);
            }
            else{
                $content = "$c<head><link type=\"text/css\" href=\"".URL."/css/toolbar.css\" rel=\"stylesheet\" /></head>$body".$content;     
            }
            if(strpos($content, "</body>")!== false) {
                $content = str_replace("</body>", $buffer.$toolbar . " </body>", $content);
            }
            else{
                $content .= $buffer. "".$toolbar;
            }
        }
        $content .="";
        return $content;
    }    
    

    private function devToolbar() {
        $mem = memory_get_usage(false);
        $mb = 1048576.2;
        $kb = 1024.2;
        //session_destroy();
        (!@session_id() ? $temp['session']=false : $temp['session'] = @session_id());
        $temp['memory'] = round($mem/$kb); 
        $temp['files'] = get_included_files();
        $temp['lang'] = $this->_kernel->page->prefered_lang ;
        $temp['page'] = [
                "controler" =>  !empty($this->_kernel->page) ? $this->_kernel->page->struct->get('controller') : "[EMPTY]", 
                "method" => !empty($this->_kernel->page) ? $this->_kernel->page->struct->get('method') : "[EMPTY]", 
                "route"=> !empty($this->_kernel->page->url) ? $this->_kernel->page->url: "[URL]", 
                "id" =>  !empty($this->_kernel->page) ? $this->_kernel->page->id : "[EMPTY]"
            ];         
            $temp['errors'] = $this->getErrors();
            $temp['class_path'] = class_exists("\App\Controller\\" . $this->_kernel->page->struct->get('controller')) ? str_replace(array(PATH,".php"), "" ,(new \ReflectionClass("\App\Controller\\" . $this->_kernel->page->struct->get('controller')))->getFilename())  : "[CONTROLER]";//$k->getFilename());
	        $temp['http'] = http_response_code();
            $temp['time'] = round(microtime(true) - $this->_kernel->start_time, 3);  
        return $temp;
    }    
    
    
    
    private function parseTemplate($tablica)
    {
        ksort($tablica);
        $wynik = "";
        foreach($tablica as $key => $value)
        {
            $wynik .= '<ol style="width: 300px;" name="folding"><label style="margin-right: 10px;">' . htmlentities($key) ."</label>";
            if(!is_array($value) && mb_strlen($value)>80) {
                $wynik .= '<span class="dev_toolbar_plus">+</span><div style="display: none; height: 200px; overflow: auto; background-color: grey;">' . htmlentities($value)."</div>";  
            }
            elseif(is_array($value)){
                $wynik .= '<span class="dev_toolbar_plus">+</span><div style="display: none; height: 200px; overflow: auto; background-color: grey;"><pre>' . print_r($value, true)."</pre></div>";
            }
            else{
                $wynik .= "- ".htmlentities($value);
            }
            $wynik .="</ol>";
        } 
        return $wynik;        
    } 
    
    public function ErrorException($errno, $errstr, $errfile, $errline)
    {
       $tmp= [];
        switch ($errno) {
            case E_USER_ERROR:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_RECOVERABLE_ERROR:
                $tmp['line'] = $errline;
                $tmp['file'] = $errfile;
                $tmp['error_number'] = $errno;
                $tmp['error'] = $errstr;
                $this->addDebugError("fatal", $tmp);                
                break;
            case E_USER_WARNING:
            case E_WARNING:
            case E_PARSE:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $tmp['line'] = $errline;
                $tmp['file'] = $errfile;
                $tmp['error_number'] = $errno;
                $tmp['error'] = $errstr;
                $this->_debug["warning"][] =$tmp;
                break;
            case E_USER_NOTICE:
            case E_NOTICE:
                $tmp['line'] = $errline;
                $tmp['file'] = $errfile;
                $tmp['error_number'] = $errno;
                $tmp['error'] = $errstr;
                $this->_debug["notice"][] =$tmp;
                break;

            default:
                break;
        }
        return true;
    }    
    
    public function getErrors($type = "")
    {
        switch($type)
        {
            default:
                return $this->_debug;
            break;
            case "":
                return $this->_debug;
            break;
            case "notice":
                return $this->_debug['notice'];
            break;
            case "warning":
                return $this->_debug['warning'];
            break;
        }
        
    }
    
    
    public function ShutDown()
    {
        //echo "asdsadasdasdasd<pre>";
        //print_r(debug_backtrace());
    }
    
}