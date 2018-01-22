<?php
namespace Core;

use App\Database\Database;
use \Core\Debug\Debug;
use \Core\Http\Request\Request;
use \Core\Route\Route;
use \Core\Exception\FatalException;
use \Core\Config\Config;
use Core\Http\Response\Response;
use Core\Objects\AppArray;
use Core\Objects\AppObject;
use lib\Core\Container;

class Bootstrap
{
    public $start_time = null;
    private $application_name = null;
    //public $controller_name = null;
    //public $method = null;
    //public $prefix = null;
    //public $lang = null;
    //public $wersje_jezykowe = null;
    public $isProd = null;
    public $config = [];
    public $page = null;

    private $booted = false;
    private $container = null;


    /**
     * Bootstrap constructor.
     * @param string $application
     * @throws FatalException
     */
    public function __construct($application_name = "", $application = '')
    {
        $this->start_time = microtime(true);
        $this->container = new Container();
        set_exception_handler(array($this, "handleException"));
        //$this->container->addBundle($this); // add this kernel to bundle
        $this->application_name = $application_name;
        if ("prod" === $application) {
            $this->isProd = true;
        } elseif ("dev" === $application) {
            $this->isProd = 0;
        } else {
            throw new FatalException("App", "No type of application specified.
             You need to set application = 'prod' - for production application or 'dev' to development application");
        }
        return $this;
    }

    /**
     * @return Container|null
     * @throws FatalException
     */
    public function getContainer()
    {
        if (null === $this->container) {
            throw new FatalException("App", "Unable to load default container");
        }
        return $this->container;
    }

    public function getApplicationPath()
    {
        return $this->application_name;
    }

    public function bootUp()
    {
        $this->config = ($this->getContainer()->addBundle(new Config))->getConfig($this->application_name);
        //$this->getContainer()->addBundle(new Config);
        //$this->config = $
        $this->getContainer()->addBundle(new View);
        $this->getContainer()->addBundle(new Database);
        $this->getContainer()->addBundle(new Database, "Baza2");
       //$this->getBundle("View")->setContainer("asdas");
        echo "<pre>";
        //print_r($this->config);//getContainer()->listBundles());
        echo "</pre>";
        if ($this->isProd === true) {
            set_error_handler(function () {
                return true;
            }); // do not show any notice or warning in production enviroment
        } else {
            $this->getContainer()->addBundle(new Debug);
        }
        foreach ($this->getContainer()->listBundles() as $name => $bundle) {
            $bundle->setContainer($this->getContainer());
            $bundle->defaultConfig($this->getContainer()->getBundleConfig($name, $this->config));
            $bundle->bootUp($this);
        }
    }

    
    private function handlePage($page, Request $request)
    {
        $this->page = new AppObject($page);
        $this->page->add("request", $request);
        $this->page->add("prefered_lang", $request->getPreferedLanguage($request->languages));
        //$this->page->prefered_lang = $request->getPreferedLanguage($request->languages);
        //  echo "<br />Jezyk strony w ktorym bedzie wyswietlana: $page->lang. Jezyk prefer
        //owany: " . $this->page->prefered_lang. "<br />";
        $controller_name = "\App\Controller\\" . $page->struct->get('controller');
        $controller = new $controller_name($this);
        return $controller->callControllerMethod($page->struct->get('method'));
    }

    
    
    public function handleRequest(Request $request)
    {
        try {
            if ($this->booted === false) {
                $this->bootUp();
            }
            // checking url
            $this->lang = $request->languages;
            return $this->handlePage((new Route())->checkUrl($request), $request);
        } catch (\Error $error) {
            return $this->HandleException($error);
        } catch (\ErrorException $e) {
            die("(*(*(*(");
        } catch (\Exception $exception) {
            return $this->HandleException($exception);
        }

      //  return new \Core\Http\Response\Response($this, ob_get_contents()); // tymczasowo zeby byla jakas tresc :)
    }
    
    // @TODO: Refactor this
    public function handleException($exception)
    {
        //echo "<pre>";
        //print_r($exception);
        //echo "</pre>";
        $temporary= new AppArray([
                "controller" => method_exists($exception, "getName") ? $exception->getName(): "Unknown name",
                "method" => __FUNCTION__,
                "prefered_lang" => isset($this->page->prefered_lang) ? $this->page->prefered_lang : "pl",
                "lang" => isset($this->page->lang) ? $this->page->lang : "pl"
            ]);
        $this->page = new AppObject(["struct" => $temporary]);
        if ($this->container === null ||  $this->container->isBundle("View") === false) { // No templates system
            http_response_code(500);
            $content = "Fatal Error occured with message <b>". $exception->getMessage() . "</b>";
            $response = new Response($this, $content, 500, false);
            echo 'asa';
            $response->sendResponse();
            return $response;
        }
        $template = $this->getContainer()->getBundle("View");
        if ($this->isProd) { // When Application is production - show error page
            $template->assign("message", method_exists($exception, "getMessage") ? $exception->getMessage() : "Unknown message");//$exception->getMessage());
            $template->assign("content", ob_get_contents());
            $content = $template->fetch("Error/Error500.tpl");
            $response = new Response($this, $content, 500);
        } else { // Development enviroment - show Exception page with debug info
            if (is_a($exception, "\Error")) {
                $template->assign("title", "Error Exception");
                $template->assign("name", "Error exception");
                $debug = [
                        "class"  => method_exists($exception, "getFile") ? $exception->getFile() : "Unknown class",
                        "line"  => method_exists($exception, "getLine") ? $exception->getLine() : "Unknown line",
                        "trace" => method_exists($exception, "getTrace") ? $exception->getTrace() : "Unknown trace"
                    ];
                $template->assign("debug_info", $debug);
            } else {
                $template->assign("title", method_exists($exception, "getTitle")
                    ? $exception->getTitle(): "Unknown title");
                $template->assign("name", method_exists($exception, "getName")
                    ? $exception->getName(): "Unknown name");
                $template->assign("debug_info", method_exists($exception, "getDebug")
                    ? $exception->getDebug() : "Unknown debug info");
            }
            $template->assign("message", method_exists($exception, "getMessage")
                ? $exception->getMessage(): "Unknown message");
            $template->assign("content", ob_get_contents());
            $content = $template->fetch("Exception/fatal.tpl");
            $response = (new Response($this, $content, 500))->addHeader("aaaa");
        }
        //$response->sendResponse();
       // echo $content;
        return $response;// new \Core\Http\Response\Response($this, $content);
    }
    
    
    public function __destruct()
    {
        if (strlen(ob_get_contents())) { //}&& $this->isProd) {
            echo "*** WARNING ***<br /> Unsend content in buffer! ";
        }
    }
    
    
    
    public function close($request, $response)
    {
        ob_end_clean();
        if ($response instanceof Response && !$response->isSend) {
            $response->sendResponse();
        }
        exit();
    }
    


    /**
     * Bootstrap::__call()
     * Funkcja wykonywana gdy klasa nie zawiera wywolywanej przez uzytkownika metody
     * @param mixed $name
     * @param mixed $arg
     * @return string
     */
    public function __call($name, $arg)
    {
        return "Call unknown method $name ";
    }





    public function sort_by_key($arr, $key)
    {
        global $key2sort;
        $key2sort = $key;
        uasort($arr, 'Bootstrap::sbk');
        return ($arr);
    }

    public static function sbk($a, $b)
    {
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
        if ($ismobile=="") {
            $ismobile =0;
        }
        $_SESSION['czyTelefon'] = $ismobile;
        $opera = (int)$wykryj->version("Opera");
        $ie =  (int)$wykryj->version("IE");
        if (!isset($_SESSION['grade'])) {
            $grade = $wykryj->mobileGrade();
            $_SESSION['grade'] = $grade;
        }
        return $ismobile;
    }


    /**
     * Bootstrap::get_real_ip()
     * Wykrywa IP uzytkownika nawet gdy łączy się przez PROXY
     * @return IP
     */
    public function get_real_ip()
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


    
    // Funkcja tylko do sprawdzania wartości zmiennej np. $_POST,wczytywana ajaxem to nie wyświetla print_r :)
    public function log_var($var)
    {
        file_put_contents(PATH_LOG . "var.log", var_export($var, true));
    }
    
     


    
    public static function log_system($modul, $komunikat, $priorytet = LOG_INFO)
    {
        openlog($modul, LOG_PID, LOG_LOCAL5);
        $data = date("Y/m/d H:i:s");
        $debug = debug_backtrace();
        if (count($debug)>0) {
            if (mb_strtolower($debug[0]['class'])!="bootstrap") { // ABY POMIJALO FUNKCJE WYWOLYWANE Z TEJ KLASY, GDY WYWOLANE ZOSTANIE np UPDATE .. to ta funkcja wywola funkcje query z tej klasy wiec błednie bedzie pokazywac poprzednia klase - nie ta z której faktycznie przyszlo zapytanie do bazy
                //$nr = 0;
            } elseif ($debug[1]['class']!="baza") {
                $nr=1;
            } else {
                $nr=2;
            }
            $plik = $debug[$nr]['class'];
            $funkcja = $debug[$nr]['function'];
        }

        //syslog(LOG_DEBUG,"Messagge: $data". debug_backtrace()[1]['function']);
        $ip = self::get_real_ip() ;
        if ($priorytet === LOG_INFO || $priorytet===LOG_DEBUG) {
            syslog($priorytet, "[$ip][$plik-$funkcja] $komunikat");
        } else {
            syslog(LOG_DEBUG, "[$ip][$plik-$funkcja] $komunikat");
        }
        closelog();

        $handle = fopen(PATH_LOG . 'debug.log', "a+");
        chmod(PATH_LOG . 'debug.log', 0777);
        fwrite($handle, "$data | $plik | $funkcja | " . $komunikat . "\n");
        fclose($handle);
    }
    
    public static function hashPassword($wejscie)
    {
        return password_hash($wejscie, PASSWORD_DEFAULT);
    }

    public static function generatePassword($dlugosc)
    {
        $pattern = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $key = "";
        $count = strlen($pattern) - 1;
        for ($i = 0; $i < $dlugosc; $i++) {
            $key .= $pattern{rand(0, $count)};
        }
        return $key;
    }
    

    public function arrayColumns($tablica, $kolumny)
    {
        $ret = array();
        if (count($tablica)>0 && count($kolumny)>0) {
            foreach ($tablica as $key => $value) {
                if (in_array($key, $kolumny)) {
                    $ret[$key] = $tablica[$key];
                }
            }
        }
        return $ret;
    }

//
//    private function instantiate($class_name, $type = "service")
//    {
//
//        try {
//            $reflection = new \ReflectionClass($class_name);
//        } catch (\ReflectionException $a) {
//        //    throw new FatalException("Failed dependency load", "Unable to load dependency. "  . $a->getMessage() . " for class: ". $class_name);
//            die("diee");
//        }
//        $constructor = $reflection->getConstructor();
//        if (!$constructor) { // If there is no constructor - add simple object
//            echo "bez constructora";
//        }
//        $dependencies = [];
//        $params = $constructor->getParameters();
//        //die("111");
//        foreach ($params as $param) {
//            try {
//                $class = $param->getClass();
//            } catch (\ReflectionException $a) {
//                throw new FatalException("Failed dependency load", "Unable to load dependency. "
//                    . $a->getMessage() . " for class: ". $class_name);
//                die("222");
//            }
//
//            if ($class) {
//                echo "<br />Klasa zalezna: ". $class->name . "<br/>";
//                if ($class->name == get_class($this)) { // if dependency is an instance of App\Bootstrap
//                    //echo "ten";
//                    $dependencies[] = $this;
//                } elseif ($this->isBundle($class->getShortName())) { // dependency could be loaded before as a bundle
//                    $dependencies[] = $this->getBundle($class->getShortName());
//                } else {
//                    $dependencies[]  = "adas" . $class->name;//$this->getService($class->name);
//                    //die();
//                }
//            }
//        }
////            echo "<br />".$class_name ."   Dependencies<pre>";
////            print_r($dependencies);
////            echo "</pre>";
//        return $this->addService($reflection->newInstanceArgs($dependencies), $reflection->getShortName());
//
//        if (!$constructor) { // If there is no constructor - add simple object
//            echo "bez constructora";
//            if ($type == "service") {
//                return $this->addService($reflection->newInstance(), $reflection->getShortName());
//            }
//            if ($type == "bundle") {
//                return $this->bundles[$reflection->getShortName()] = $reflection->newInstance();//$this->addService($reflection->newInstance(), $reflection->getShortName());
//            }
//        }
//
//        if ($type == "service") {
////            echo "Dependencies<pre>";
////            print_r($dependencies);
////            echo "</pre>";
//        //    return $this->addService($reflection->newInstance(), $reflection->getShortName());
//            return $this->addService($reflection->newInstanceArgs($dependencies), $reflection->getShortName());
//        }
//        if ($type == "bundle") {
//            return $this->bundles[$reflection->getShortName()] = $reflection->newInstanceArgs($dependencies);//$this->addService($reflection->newInstance(), $reflection->getShortName());
//        }
//    }



}
