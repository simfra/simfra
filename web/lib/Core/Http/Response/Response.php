<?php
namespace Core\Http\Response;

class Response
{
    const HTTP_OK= 200;
    const HTTP_NOT_FOUND = 404;
    private $headers = array();
    private $kernel = null;
    private $useDebug = true;
    public $isSend = false;
    public $content="";

    
    public function __construct(\Core\Kernel $kernel, $content = "", $response_code = "", $useDebug = true)
    {
        $this->kernel = $kernel;
        if ($response_code!="") {
            http_response_code($response_code);
        }
        //header("Content-Type: text/html");
        $this->useDebug = $useDebug;
        $this->addHeader("Content-Type: text/html");
        //header('Content-Length: '.strlen($content));

        $this->content = $content;
                //ob_end_clean();
        //header('Connection: close');
        //return $this;
    }
    
    
    private function sendHeaders($headers)
    {
        if (!headers_sent()) {
            foreach ($headers as $header) {
                header($header);
            }
        }
    }
    
    private function sendContent($content)
    {
        if (ob_get_level()) {
            ob_end_clean();
        }
        echo $content;
    }
    
    public function sendResponse()
    {
        //global $app;
        //die();
        //$this->content .="IP:". print_r($app, true);
        // Devtool if Debug mode is enable
        //echo "aaaa";
        if ($this->useDebug === true && $this->kernel->getContainer()->isBundle("Debug") && !$this->kernel->isProd) {
            try {
                $this->content = $this->kernel->getContainer()->getBundle("Debug")->makeDevToolbar($this->content);
            } catch (\Exception $e) {
                die("Exception from Debug ".$e->getMessage());
            }
        }
        $this->addHeader('Content-Length: '.strlen($this->content));
        $this->sendHeaders($this->headers);
        $this->sendContent($this->content);
        $this->isSend = true;
        exit();
    }
    
    public function addHeader($header_to_add)
    {
        $this->headers[] = $header_to_add;
        return $this;
    }
}