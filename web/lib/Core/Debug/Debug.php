<?php
namespace Core\Debug;
use Core\Controller;
use Core\Objects\App_Array;
use Core\Objects\App_Object;
use lib\Core\Bundle;
use Core\Session\Session;


class Debug extends Bundle
{
    private $debug = [];
    public $show_buffer = false;
    const SESSION_NAME = "SIMFRA_DEBUG";

    public function __construct()
    {
        //echo "PATH: " . __DIR__ . " " . __CLASS__;
        error_reporting(E_ALL);
        //session_start(["session_name" => SESSION_NAME]);

        ini_set('display_errors', 1);
        $this->debug = ["notice"=>[], "warning"=>[]];
        set_error_handler(array($this, "errorException"));
        register_shutdown_function([$this, "shutDown"]);
        $this->show_buffer = true;
        //var_dump(parent::class);

        //print_r($_SESSION);

        //trigger_error("Error Message", E_USER_WARNING);
    }
    
    public function addDebugError($type, $error)
    {
        //echo "Typ: $type\n";
        $this->debug[$type][] = $error;
        //echo "*****************<pre>";
        //print_r($this->debug[$type]);
        //echo "</pre>";
    }
    
    public function makeDevToolbar($content, $show_buffer = true)
    {
//echo "<pre>"; print_r($this->getContainer()->getBundle("Config")); echo "</pre>";

        //Session::start($this::SESSION_NAME);

        if ($this->isBundle("View")) {
            $tpl = $this->getBundle("View");
            //$tpl->assign("path_www", URL);
            //echo "<pre>"; print_r($this->getBundle("Debug")); echo "</pre>";
            //$tpl->assign("ismobile", $this->kernel->isMobile);
            $tpl->assign("dev_templates", $this->parseTemplate($tpl->get_template_vars()));
            if ($this->show_buffer == true && mb_strlen(ob_get_contents()) && $show_buffer === true) {
                $buffer = '<div id="devtoolbar_buffer"><h2>Buffered output</h2><div>'.ob_get_contents()."</div></div>";
            } else {
                $buffer = "";
            }
            ob_end_clean();
            $tpl->assign("dev", $this->devToolbar());
            $toolbar = $tpl->fetch("Debug/toolbar.tpl");
            $head = "";
            (strpos($content, "<!DOCTYPE html>")=== false) ? $head = "<!DOCTYPE html>" . $head : "";
            (strpos($content, "<body")=== false) ? $body = "<body>" : $body = "";
            if (strpos($content, "</head>")!== false) {
                $content = str_replace("</head>", '<link type="text/css" href="/css/toolbar.css" rel="stylesheet" /></head>' . $body, $content);
            } else {
                $content = $head . '<head><link type="text/css" href="/css/toolbar.css" rel="stylesheet" /></head>' . $body . $content;
            }

            if (strpos($content, "</body>")!== false) {
                $content = str_replace("</body>", $buffer . $toolbar . " </body>", $content);
            } else {
                $content .= $buffer . "" . $toolbar;
            }
        }
        $content .="";
        return $content;
    }
    

    private function devToolbar()
    {
        $mem = memory_get_usage(false);
        (!@session_id() ? $temp['session']=false : $temp['session'] = @session_id());
        $kernel = $this->getKernel();
        $temp['memory'] = round($mem/1024);
        $temp['files'] = get_included_files();
        $temp['lang'] = $kernel->page->prefered_lang;
        $temp['page'] = [
                "controler" => !empty($kernel->page) ? $kernel->page->struct->get('controller') : "[EMPTY]",
                "method" => !empty($kernel->page) ? $kernel->page->struct->get('method') : "[EMPTY]",
                "route"=> !empty($kernel->page->url) ? $kernel->page->url : "[URL]",
                "id" =>  !empty($kernel->page) ? $kernel->page->id : "[EMPTY]"
            ];
            $temp['class_path'] = class_exists(PATH_USER_CONTROLLER . $kernel->page->struct->get('controller'), false)
                ? str_replace(array(PATH,".php"), "", (new \ReflectionClass("\App\Controller\\"
                    . $kernel->page->struct->get('controller')))->getFilename())  : "[CONTROLER]";
            $temp['http'] = http_response_code();
            $temp['time'] = round(microtime(true) - $kernel->start_time, 3);
            $temp['errors'] = $this->debug;
        return $temp;
    }
    
    
    
    private function parseTemplate($table)
    {
        ksort($table);
        $result = "";
        foreach ($table as $key => $value) {
            $result .= '<ol name="folding"><label">' . htmlentities($key) ."</label>";
            if (!is_array($value) && mb_strlen($value)>80) {
                $result .= '<span class="dev_toolbar_plus">+</span><div>' . htmlentities($value)."</div>";
            } elseif (is_array($value)) {
                $result .= '<span class="dev_toolbar_plus">+</span><div><pre>' . print_r($value, true)."</pre></div>";
            } else {
                $result .= "- ".htmlentities($value);
            }
            $result .="</ol>";
        }
        return $result;
    }
    
    public function errorException($errno, $errstr, $errfile, $errline)
    {
        $tmp= [];
        $tmp['line'] = $errline;
        $tmp['file'] = $errfile;
        $tmp['error_number'] = $errno;
        $tmp['error'] = $errstr;
        switch ($errno) {
            case E_USER_ERROR:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_RECOVERABLE_ERROR:
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
                $this->addDebugError("warning", $tmp);
                break;
            case E_USER_NOTICE:
            case E_NOTICE:
                $this->addDebugError("notice", $tmp);
                break;
            default:
                $this->addDebugError("notice", $tmp);
                break;
        }
        return true;
    }
    
    public function getErrors($type = "")
    {
//        echo "***<pre>";
//        print_r($this->debug);
//        echo "</pre>";
        switch ($type) {
            default:
                return $this->debug;
            break;
            case "":
                return $this->debug;
            break;
            case "notice":
                return $this->debug['notice'];
            break;
            case "warning":
                return $this->debug['warning'];
            break;
        }
    }
    
    
    public function shutDown()
    {
        //echo "<pre>";
        //print_r(debug_backtrace());
        //echo "</pre>";
    }
    
}