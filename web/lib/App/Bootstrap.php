<?php
namespace App;
use \App\Debug\Debug;
use \Core\session;
use \Core\Http\Request\Request;
use \App\Route\Route;
use \Core\Exception\FatalException;
use \App\View;
use \App\Database\Database;

use \App\Config\Config;
use \Error;
use Core\Objects\App_Array;
use Core\Objects\App_Object;
class Bootstrap {
    public $_url = null;
    private $_controller = null;
    public $_controller_name = null;
    public $_method = null;
    private $_tpl = null;
    public $prefix = null;
    public $lang = null;
    public $wersje_jezykowe = null;
    public $url_images = null;
    private $_db = array();
    public $_debug = null;
    public $strona = array();
    public $powiadomienia = array();
    public $isProd = null;
    public $config = null;
    private $_bundles = [];
    public $page = null;
    
    function __construct($application="")
    {
        $this->start_time = $time_start = microtime(true);
        $this->config = new \App\Config\Config();         
        $this->addBundle(new \App\View);
        set_exception_handler(array($this, "HandleException"));
        if($application === "prod") {
            $this->isProd = 1;
            set_error_handler(function() { return true; } ); // do not show any notice or warning in production enviroment
        }
        elseif($application === "dev"){
            $this->isProd = 0;
            $this->addBundle(new Debug($this));
            set_error_handler(array($this->getBundle("Debug"), "ErrorException"));
        }
        else{
            throw new FatalException("App","No application specified. You need to set application = 'prod' - for production application or 'dev' to development application");
        }        
        return $this;
    }    
    
    
    public function isBundle($bundle)
    {
        return isset($this->_bundles[$bundle]) ? true : false;
    }
    
    
    public function addBundle($bundle, $name = "")
    {
        if($name == "") { // default name will be class name
            $name = (new \ReflectionClass($bundle))->getShortName();
        }
        if(!isset($this->_bundles[$name])) {
            $this->_bundles[$name] = $bundle;
            return true;
        }
        else{
            echo "Już dodane";
            return false;
        }

    }
    
    
    public function getBundle($name)
    {
        if(isset($this->_bundles[$name])) {
            return $this->_bundles[$name];
        }
        else{
            throw new FatalException("Bundle", "No bundles with specified name ($name)");
            return null;
        }
    }
    
    public function getDatabase()
    {
        if(isset($this->_bundles["Database"])) {
            return $this->_bundles["Database"];
        }
        else{
            $this->addBundle(new \App\Database\Database($this));
            return $this->getBundle("Database");
        }
    }
    
    
    public function delBundle($bundle)
    {
        if($this->isBundle($bundle)) {
            unset($this->_bundles[$bundle]);
        }
    }
    
    

    
    public function BundleList()
    {
        $list = array();
        foreach($this->_bundles as $key=>$value)
        {
            $list[$key] = get_class($value);
        }    
        return $list;
    }
    
    
    private function HandlePage($page, Request $request)
    {

//         $this->_debug = new App_Object(array("notice"=>new App_Array(), "warning"=> new App_Array()));;
        $this->page = new App_Object($page);  
        $this->page->add("request", $request);          
        $this->page->prefered_lang = $request->getPreferedLanguage($request->_languages);             
        echo "<br />Jezyk strony w ktorym bedzie wyswietlana: $page->lang. Jezyk preferowany: " . $this->page->prefered_lang. "<br />";
//        echo "<pre>";
//        print_r($this->page);
//        echo "</pre>";       
        
        //echo $this->page->request->query->get("method"); 
        $controller_name = "\App\Controller\\" . $page->struct->get('controller');
        $controller = new $controller_name($this);
        return $controller->callControllerMethod($page->struct->get('method'));
//        $content = ob_get_contents();// tymczasowo zeby byla jakas tresc :)
    }     

    
    
    public function HandleRequest(Request $request)
    {
        // checking url
        try{
            $route = new \App\Route\Route();
            $this->lang = $request->_languages;
            return $this->HandlePage($route->checkUrl($request), $request); 
        }
        catch(Throwable $t) // php <7 
        {
            die("Throwable Error php < 7");
        }
        catch(\Error $error)
        {
            return $this->HandleException($error);
        }
        catch(\ErrorException $e) {
            die("(*(*(*(");
        }
        catch(\Exception $exception)
        {

            return $this->HandleException($exception);
        }
       return new \Core\Http\Response\Response($this,ob_get_contents()); // tymczasowo zeby byla jakas tresc :)
    }
    
    
    public  function getTpl()
    {
        return self::getBundle("View");
    }
    

    
    public function HandleException($exception)
    {
        $temporary= new App_Array([
                "controller" => method_exists($exception, "getName") ? $exception->getName(): "Unknown name",
                "method" => __FUNCTION__,
                "prefered_lang" => isset($this->page->prefered_lang) ? $this->page->prefered_lang : "pl",
                "lang" => isset($this->page->lang) ? $this->page->lang : "pl"
            ]);
        $this->page = new App_Object(["struct" => $temporary]);
        if($this->isBundle("View") === false) { // No templates system 
            http_response_code(500);
            $content = "Fatal Error occured with message <b>". $exception->getMessage() . "</b>";
            $response = new \Core\Http\Response\Response($this, $content,500);
            return $response;
        }
        $template = $this->getTpl();    
        if($this->isProd) { // When Application is production - show error page
            $template->assign("message", method_exists($exception, "getMessage") ? $exception->getMessage() : "Unknown message");//$exception->getMessage());
            $template->assign("content", ob_get_contents());
            $content = $template->fetch("Error/Error500.tpl");
            $response = new \Core\Http\Response\Response($this, $content,500);
        }
        else{ // Development enviroment - show Exception page with debug info
            if(is_a($exception,"\Error")) {
                $template->assign("title", "Error Exception");   
                $template->assign("name", "Error exception");
                $debug = 
                    [
                        "class"  => method_exists($exception, "getFile") ? $exception->getFile() : "Unknown class",
                        "line"  => method_exists($exception, "getLine") ? $exception->getLine() : "Unknown line",
                        "trace" => method_exists($exception, "getTrace") ? $exception->getTrace() : "Unknown trace"
                    ];
                $template->assign("debug_info", $debug);                 
            }
            else{
                $template->assign("title", method_exists($exception, "getTitle") ? $exception->getTitle(): "Unknown title");
                $template->assign("name", method_exists($exception, "getName") ? $exception->getName(): "Unknown name");
                $template->assign("debug_info", method_exists($exception, "getDebug") ? $exception->getDebug(): "Unknown debug info");                                                                
            }
            $template->assign("message", method_exists($exception, "getMessage") ? $exception->getMessage(): "Unknown message");  
            $template->assign("content", ob_get_contents());
            $content = $template->fetch("Exception/fatal.tpl");
            $response = new \Core\Http\Response\Response($this, $content,500);
        }
      return $response;// new \Core\Http\Response\Response($this, $content);
    }
    
    
    function __destruct()
    {
        if(strlen(ob_get_contents()) ){//}&& $this->isProd) {
            echo "*** WARNING ***<br /> Unsend content in buffer!";    
        }
    }
    
    
    
    public function Close($request, $response)
    {
        ob_end_clean();
        if( $response instanceof \Core\Http\Response\Response && !$response->isSend ) {
            $response->sendResponse();
        }
        exit();
    }
    


    /**
     * Bootstrap::__call()
     * Funkcja wykonywana gdy klasa nie zawiera wywolywanej przez uzytkownika metody 
     * @param mixed $name
     * @param mixed $arg
     * @return
     */
    public function __call($name, $arg)
    {
        return "Call unknown method $name ";
    }


    /**
     * Bootstrap::getDB()
     * Zwraca obiekt bazy w zalezności od rodzaju bazy (domyslnie: main) - moze istniec kilka baz do ktorych moze polaczyc sie skrypt
     * @param string $rodzaj - rodzaj bazy (jej identyfikator) ktorej obiekt ma byc zwrocony
     * @return
     */
    public function getDB($rodzaj = "main") 
    {
        if (!isset($this->_db[$rodzaj]) or $this->_db[$rodzaj] == "") {
            $this->_db[$rodzaj] = new baza($rodzaj);
        }
        if (isset($this->_db[$rodzaj])) {
            return $this->_db[$rodzaj];
        }

    }


    public function sort_by_key($arr, $key) 
    {
        global $key2sort;
        $key2sort = $key;
        uasort($arr, 'Bootstrap::sbk');
        return ($arr);
    }

    static function sbk($a, $b) {
        global $key2sort;
        return (strcasecmp($a[$key2sort], $b[$key2sort]));
    }
    

    /**
     * Bootstrap::sprawdzMobile()
     * Sprawdza czy użytkownik korzysta z urządzenia mobilnego
     * @return 0 - jezeli nie, 1 - jezeli mobil
     */
    public function sprawdzMobile() 
    {
        global $config, $params;
        $ismobile = 0;
        $wykryj = new Mobile_Detect;
        $ismobile = $wykryj->isMobile();
        if($ismobile=="") {
            $ismobile =0;
        }
        $_SESSION['czyTelefon'] = $ismobile;
        $opera = (int)$wykryj->version("Opera");
        $ie =  (int)$wykryj->version("IE");
        if(!isset($_SESSION['grade']))
        {
            $grade = $wykryj->mobileGrade();
            $_SESSION['grade'] =$grade;     
        }
        return $ismobile;
    }


    // załaduj kontroler w zależności od urla
    public function init() 
    {

        global $config, $page;
        $this->_db = ""; 
//        $this->_getUrl();
        if (isset($_GET['url'])) {
            $url = ltrim($_GET['url'], '/');
        } else {
            $url = "";
        }

        if (isset($_GET['classic'])) {
            if ($_GET['classic'] == "true") {
                $_SESSION['ismobile'] = 0;
            } else {
                $_SESSION['ismobile'] = 1;
            }
        }

        if (!isset($_SESSION['ismobile'])) {
            $_SESSION['ismobile'] = $this->sprawdzMobile();
        }
        
        

        
        

        $cms = new CMSpage;
        // SPRAWDZANIE PODANEGO ADRESU W STRUKTURZE STRONY
        $strona = $cms->checkURL($url);
        
        if (is_array($strona) && isset($strona['id'])) {
            if (isset($strona['args'])) {
                $temp['args'] = $strona['args']; // ZAWIERA TABLICE PARAMETROW Z URL (ZE ZMIENNYCH)
                $strona = array_merge($page[$strona['id']], $temp);
            } else {
                $strona = $page[$strona['id']];
            }
            $this->strona = $strona;
            
            if ($this->strona['typ'] == "content") {
                          
                if(isset($_SESSION['ACTUAL_ID']))
                {
                    $_SESSION['PREV_ID'] = $_SESSION['ACTUAL_ID'];
                }
                $_SESSION['ACTUAL_ID'] = $strona['id'];
                
            }            
            
            
            
            
            // DEBUG


            $this->debug['page']['controler'] = $this->strona['controller'];
            $this->debug['page']['method'] = $this->strona['method'];
            $this->debug['page']['id'] = $this->strona['id'];
            $this->debug['lang'] = $this->prefix;
            //$this->debug['page']['url'] = $this->getUrl();
            $this->debug['page']['route'] = $this->strona['url'][$this->prefix];
            //$this->debug['a']=$strona;
//echo $strona['controller'];
            if ($this->strona['typ'] == "redirect") {
                $this->redirect($this->strona['target']);
            } else {
                if ($this->checkControlerExists($this->strona['controller']."_controller") == true) {
                    $nazwa = $this->strona['controller']."_controller";
                    $this->_controller = new $nazwa;
                    $this->_controller_name = $nazwa;//$strona['controller'];                    

                    $k = new ReflectionClass($nazwa);

                    $this->debug['files'] = get_included_files();
                    $this->debug['class_path'] = str_replace(array(PATH,".php"), "" ,$k->getFilename());
                    $this->debug['http'] =  http_response_code();//$_SERVER["REDIRECT_STATUS"];
                    if (method_exists($this->strona['controller']."_controller", $this->strona['method'])) {
                        $this->_method = $this->strona['method'];
                        if ($this->_controller->loadModel($this->strona['controller'], PATH_MODEL) == true) {
                            
                            // SPRAWDZENIE CZYSTRONA WYMAGA ZALOGOWANIA
                            if(isset($this->strona['https']) && $this->strona['https'] == 1)
                            {                 
                                if(!session::isLogIn())
                                {
                                    //die("Wylogowywanie");
                                    $this->redirect(6); // Przekierowanie na logowanie
                                    exit();
                                }
                            }
                            
                            if(session::isLogIn() && session::get('USER', 'zmiana_hasla') == 't' && $this->strona['id'] != '225' && $this->strona['id'] != '9' && $this->strona['id'] != '1000') // 9 -wylogowanie, 1000 - jezyk
                            {
                                $this->redirect(225); // Przekierowanie na zmiane hasła
                                exit();
                            }             
                                                       
                            if (method_exists($this->strona['controller'] . '_model', $this->strona['method'])) {
                                if(isset($this->strona['load_lang']) && $this->strona['load_lang'] == '1') {
                                    require PATH_LANG . $this->prefix . '.php';
                                    $this->lang = $lang;   
                                                                     
                                }

                                
                                if(isset($this->strona['pre_defined']) && $this->strona['pre_defined'] != "")
                                {
                                    $tmp = explode('&', $this->strona['pre_defined']);
                                    $pre_def = array();
                                    foreach($tmp as $t)
                                    {
                                        $tmp2 = explode('=', $t);
                                        $pre_def[$tmp2[0]] = $tmp2[1];
                                    }
                                    $strona['pre_def'] = $pre_def;
                                }                                


                                // Sprawdzenie czy jest np. przerwa techniczna
                                $this->check_site_status($this->strona);

                                
                                $this->_controller->{$this->strona['method']}($this->strona);
                                
                                if ($this->strona['typ'] == "content")
                                {
                                    $this->_controller->view->render($this->strona);
                                }
                                if ($this->strona['typ'] == "notemplate")
                                {
                                     $this->_controller->view->render_notemplate($this->strona);
                                }

                            } else {
            //                    echo "Brak metody (" . $strona['method'] . ") w modelu " . $strona['controller'];
				    $this->error();
                            }
                        } else {
//                            echo "brak modelu " . $strona['controller'];
				$this->error();
                        }
                    } else {
                        //echo "Brak metody (" . $strona['method'] . ") w kontrolerze: " . $strona['controller'];
			$this->error();
                    }
                } else {
                    //echo "Brak kontrolera:" . $strona['controller'];
		    $this->error();
                }
            }
        } elseif ($strona === "multi") // GDY ZWROCONO WIECEJ NIZ JEDNEN LINK
        {
//            echo "Bład: Wiecej niz jeden wpis z tym samym linkiem!. Sprawdz struct.php";
	    $this->error();
        } else {
	    $this->error();
        }
        return;
    }


    
    /**
     * Bootstrap::get_real_ip()
     * Wykrywa IP uzytkownika nawet gdy łączy się przez PROXY
     * @return IP
     */
    public  function get_real_ip() 
    {
        //return "asdas";
        $ip = $_SERVER['REMOTE_ADDR'];
        if (empty($ip) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (empty($ip) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        // Gdy jest proxy wiec ten adres jest ip rzeczywistym
        echo $ip;
        return $ip;
    }
    /**
     * Bootstrap::log()
     * Logowanie komunikatów do pliku 
     * @param mixed $tekst
     * @return void
     */
    public function log($tekst, $plik = "mvc.log") 
    {
//        echo $tekst;
        global $config;
        $handle = fopen(PATH . "log/".$plik, "a+");
        fwrite($handle, "" . date("d-m-Y H:i:s") . " | " . $this::get_real_ip() . " | " . $tekst . "\n");
        fclose($handle);
    }

    /**
     * Bootstrap::redirect()
     * 
     * @param mixed $id
     * @param mixed $url
     * @param mixed $params
     * $params = array("name" => "value", "name2" => "value2"...)
     * @return void
     */
    public function redirect($id = false, $url = false, $params = false) 
    {
       // echo "Przekierowanie";
        global $page, $app;
       http_response_code(303);
        
        $link = "";

        if ($id !== false) {
            foreach ($page as $p) {
                if ($p['id'] == $id) {
                    $link = $p['url'][$app->prefix];
                }
            }
        } else
            if ($url !== false) {
                foreach ($page as $p) {
                    foreach ($p['url'] as $u) {
                        $tmp = explode('/', $u);
                        $link = array();
                        foreach($tmp as $t)
                        {
                            $x = explode('|', $t);
                            $link_tmp = $x[0];
                            if(count($x) > 1)
                            {
                                $link_tmp .= ']';
                            }
                            $link[] = $link_tmp;
                        }

                        $link = implode('/', $link);
                        
                        if ($link == $url) {
                            $link = $p['url'][$app->prefix];
                        }
                    }
                }
            }

        $tmp = explode('/', $link);
        $link = array();
        foreach($tmp as $t)
        {
            $x = explode('|', $t);
            $link_tmp = $x[0];
            if(count($x) > 1)
            {
                $link_tmp .= ']';
            }
            $link[] = $link_tmp;
        }
        
        
        
        $link = implode('/', $link);

        if ($params !== false) {
            foreach ($params as $key => $value) {
                $link = str_replace('[' . $key . ']', $value, $link);
            }
        }
        
        
        // Jerśli gdzieś występuje stronicowanie to ustawia parametr strona na 1
        $link = str_replace('[strona]', '1', $link);        
                

        if($link != "")
        {
            ob_flush();
            header("Location: $link");            
            exit();
        }else{
            //echo "Błędne przekierowanie!";
	    $this->error();
        }
        //exit();
    }

    
    
    // Funkcja tylko do sprawdzania wartości zmiennej np. $_POST, bo jak strona wczytywana ajaxem to nie wyświetla print_r :)
    public function log_var($var)
    {
        file_put_contents(PATH_LOG . "var.log", var_export($var, true));
    }
    
     
    
    public function check_site_status($strona)
    {
        global $app, $config;
               
        switch($config['status_strony'])
        {
            case '0': // Dostępna już wkrótce
                $strony_dostepne = array('1', '1001', '1003', '1004', '1006', '201', '203', '205', '208', '211', '214', '313', '314', '315','316','301');
                if(!in_array($strona['id'], $strony_dostepne))
                {
                    $app->redirect(1); // strona startowa
                }
                
            break;
            case '1': break; // Strona w pełni funkcjonalności
            case '2': break;// Tylko odczyt z bazy
            case '3': // przerwa techniczna
                session_destroy();
                if($strona['id'] == '350')
                {                    
                    $app->_controller->{$strona['method']}($strona);
                    $app->_controller->view->render_notemplate($strona);
                }else{
                    echo "Bootstrap::check_site_status() - ustawić redirect na strone przerwy technicznej"; exit();
                    //$app->redirect();
                }
                exit();
            break;
        }
        
    }
    

    
    public static function log_system($modul, $komunikat, $priorytet=LOG_INFO)
    {
        openlog($modul, LOG_PID, LOG_LOCAL5);
        $data = date("Y/m/d H:i:s");
        $debug = debug_backtrace();
        if(count($debug)>0)
        {
        if(mb_strtolower($debug[0]['class'])!="bootstrap") { // ABY POMIJALO FUNKCJE WYWOLYWANE Z TEJ KLASY, GDY WYWOLANE ZOSTANIE np UPDATE .. to ta funkcja wywola funkcje query z tej klasy wiec błednie bedzie pokazywac poprzednia klase - nie ta z której faktycznie przyszlo zapytanie do bazy
            $nr = 0;
        }
        elseif($debug[1]['class']!="baza") {
            $nr=1;
        }
        else {
            $nr=2;
        }
        $plik = $debug[$nr]['class'];
        $funkcja = $debug[$nr]['function'];
        }        

        //syslog(LOG_DEBUG,"Messagge: $data". debug_backtrace()[1]['function']);
        $ip = self::get_real_ip() ;
        if($priorytet===LOG_INFO || $priorytet===LOG_DEBUG) {
            syslog($priorytet,"[$ip][$plik-$funkcja] $komunikat");
        }   
        else {
            syslog(LOG_DEBUG,"[$ip][$plik-$funkcja] $komunikat");
        }       
        closelog();

        $handle = fopen(PATH_LOG . 'debug.log', "a+");
        chmod(PATH_LOG . 'debug.log', 0777);
        fwrite($handle, "$data | $plik | $funkcja | " . $komunikat . "\n");
        fclose($handle);
    } 
    
    public static function hash_password($wejscie)
    {
        return password_hash($wejscie, PASSWORD_DEFAULT);
    }

    public static function generate_password($dlugosc) 
    {
        $pattern = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $key = "";
        $count = strlen($pattern) - 1;
        for ($i = 0; $i < $dlugosc; $i++) {
            $key .= $pattern{rand(0, $count)};
        }
        return $key;
    }
    

    public function array_columns($tablica, $kolumny)
    {
        $ret = array();
        if(count($tablica)>0 && count($kolumny)>0)
        {
            foreach($tablica as $key=>$value)
            {
                if(in_array($key, $kolumny)) {
                    $ret[$key] = $tablica[$key];
                }
            }
        }
        return $ret;
    }
    
    

}
