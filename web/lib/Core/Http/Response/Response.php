<?php
namespace Core\Http\Response;
class Response {
    const HTTP_OK= 200;
    const HTTP_NOT_FOUND = 404;
    public $content="";
    private $_headers = array();
    public $isSend = false;
    private $_kernel = null;
    
    public function __construct(\App\Bootstrap $kernel,  $content="", $response_code="")
    {
        //echo $content;
        $this->_kernel = $kernel;
        if($response_code!="") {
                    http_response_code($response_code);
        }
        //header("Content-Type: text/html");
        $this->addHeader("Content-Type: text/html");
        //header('Content-Length: '.strlen($content));        

        $this->content = $content;
                //ob_end_clean();
        //header('Connection: close');
    }
    
    
    private function sendHeaders($headers)
    {
        if(!headers_sent()) {
            foreach($headers as $header)
            {
                header($header);
            }
        }          
    }
    
    private function sendContent($content)
    {
        ob_end_clean();                        
        echo $content;        
    }
    
    public function sendResponse()
    {
        //global $app;
        //die();
        //$this->content .="IP:". print_r($app, true);
        // Devtool if Debug mode is enable
        if($this->_kernel->isBundle("Debug") && $this->_kernel->isProd==0) {
            try{
                $this->content = $this->_kernel->getBundle("Debug")->makeDevToolbar($this->content);
            }
            catch(\Exception $e)
            {
                die("Exception from Debug ".$e->getMessage());
            }
        }
        $this->addHeader('Content-Length: '.strlen($this->content));
        $this->sendHeaders($this->_headers);
        $this->sendContent($this->content);
        $this->isSend = true;    
        exit();
    }
    
    public function addHeader($header_to_add)
    {
        $this->_headers[] = $header_to_add;
    }
}