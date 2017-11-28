<?php
namespace Core;

use \Core\Debug\Debug;
//use \Core\session;
use \Core\Http\Request\Request;
use \Core\Route\Route;
use \Core\Exception\FatalException;
use \App\Database\Database;

use \Core\Config\Config;
//use \Error;
use Core\Http\Response\Response;
use Core\Objects\App_Array;
use Core\Objects\App_Object;
use lib\Core\Bundle;
use lib\Core\Container;


//use \App\DependencyInjector;

class Bootstrap
{
    //public $url = null;
    public $start_time = null;
    //public $controller_name = null;
    //public $method = null;
    //public $prefix = null;
    //public $lang = null;
    //public $wersje_jezykowe = null;
    //public $url_images = null;
    //public $debug = null;
    //public $strona = array();
    //public $powiadomienia = array();
    public $isProd = null;
    public $config = [];
    public $page = null;
    private $booted = false;

    private $bundles = [];
    private $services = [];

    private $container = null;


    public function __construct($application = "")
    {
        $this->start_time = microtime(true);
        $this->container = new Container();
        set_exception_handler(array($this, "handleException"));
        if ("prod" === $application) {
            $this->isProd = true;
        } elseif ("dev" === $application) {
            $this->isProd = 0;
        } else {
            throw new FatalException("App", "No type of application specified. You need to set application = 'prod' - for production application or 'dev' to development application");
        }
        return $this;



//        echo "<pre>";
//        print_r($this->bundles);
//        echo "</pre>";
        //return $this;

        $this->config = ($this->addBundle(new Config))->getConfig();
        $this->addBundle(new View);
        set_exception_handler(array($this, "handleException"));
        if ($application === "prod") {
            $this->isProd = true;
            set_error_handler(function () {
                return true;
            }); // do not show any notice or warning in production enviroment
        } elseif ($application === "dev") {
            $this->isProd = 0;
            $this->addBundle(new Debug);
        } else {
            throw new FatalException("App", "No type of application specified. You need to set application = 'prod' - for production application or 'dev' to development application");
        }
        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }


    public function bootUp()
    {
        $this->config = ($this->container->addBundle(new Config))->getConfig();
        $this->container->addBundle(new View);
       //$this->getBundle("View")->setContainer("asdas");
//        echo "<pre>";
//        print_r($this->container->listBundles());
//        echo "</pre>";


        if ($this->isProd === true) {
            set_error_handler(function () {
                return true;
            }); // do not show any notice or warning in production enviroment
        } else {
            $this->container->addBundle(new Debug);
        }

        foreach ($this->container->listBundles() as $bundle) {
            $bundle->setContainer($this->container);
        }
    }
    

    
    
    public function aaddBundle($bundle, $name = "")
    {
        // $config = ["kuku" => 1, "muniu" => 2];
        //return $this->instantiate($bundle, "bundle");
        $reflection = new \ReflectionClass($bundle);
        if ($name == "") { // default name will be class name
            $name = $reflection->getShortName();
        }
        echo "Add bundle: $name\n";
        if (!array_key_exists($name, $this->bundles)) {
            //$bundle->defaultConfig($this->getBundleConfig($name));
            echo "aaa<br />";
            $this->bundles[$name] = $bundle;
        }
//        echo "<pre>";
//        print_r($this->bundles);
//        echo "</pre>";
        return $this->bundles[$name];
        /*
        $reflection = new \ReflectionClass($bundle);
        echo "   ^^^   ";
        $constructor = $reflection->getConstructor();


        //echo "Name : $name";
        if (!$constructor) { // If there is no constructor - add simple object
          //  echo "aaa";
            return $this->bundles[$name] = $bundle;//$reflection->newInstance();
        }
        //echo"2";
        $dependencies = [];
        $params = $constructor->getParameters();
        //print_r($params);
        foreach ($params as $param) {
            try {
                $class = $param->getClass();
            } catch (\ReflectionException $a) {
                throw new FatalException("Failed dependency load", "Unable to load dependency. "
                    . $a->getMessage() . " for class: ". $class_name);
            }
            if ($class) {
                echo "Klasa $class->name";
                if ($class->name == get_class($this)) { // if dependency is an instance of App\Bootstrap
                    $dependencies[] = $this;
                } elseif ($this->isBundle($class->getShortName())) { // dependency could be loaded before as a bundle
                    $dependencies[] = $this->getBundle($class->getShortName());
                } else {
                    $dependencies[] = $this->addService($this->instantiate($class->name), $class->getShortName());
                }
            }
        }
//        echo "<pre>@@@";
//        if ($this->isBundle("Config") && isset($this->config['bundles'][strtolower($name)])) {
//            print_r($this->config['bundles']);
//        }
//        echo "</pre>";

        $this->bundles[$name] = $reflection->newInstanceArgs($dependencies);
        ($this->bundles[$name])->defaultConfig($this->getBundleConfig($name));
        return $this->bundles[$name];
        if (!isset($this->bundles[$name])) {
            //$a = new \ReflectionClass($bundle);
            //$bundle->defaultConfig($config);
             // (new \ReflectionClass($bundle))->newInstance();/* function () {
            foreach ($config as $key => $value) {
       //         $bundle->{$key} = $value;
            }

            echo "<pre>";
            print_r($name);
            echo "</pre>";
            //$this->bundle[$name] = $bundle;
            return $this->bundles[$name] = $bundle;//$reflection->newInstance();
                /*$obj = new \ReflectionClass($bundle);

                $obj->name = $config["kuku"];
                return $obj;
            };*/


            //$reflection->newInstance();
            // function () use ($config) {                 $obj = new \ReflectionClass($bundle);
            //                 return $obj->newInstance();             };
//            $config = ["kuku" => 1, "muniu" => 2];//$this->config;
//            $cl = new \ReflectionClass($bundle);
//            $obj = $cl->newInstanceArgs($config);
//            $obj->aaa = "asdasd";
//            return $this->_bundles[$name] = $obj;// (new \ReflectionClass($bundle))->newInstanceArgs($config);
/*
        } else { // already added
            return false;
        }*/
    }


    private function getBundleConfig($name)
    {
        if (!array_key_exists("bundles", $this->config)) {
            return [];
        }
        foreach ($this->config['bundles'] as $key => $value) {
            if (mb_strtolower($key) == mb_strtolower($name)) {
                return $this->config['bundles'][$key];
            }
        }
        return [];
    }

    public function getService($service_name)
    {
        return (array_key_exists($service_name, $this->services))
            ? $this->services[$service_name] : die("nie ma takiej uslugi" . $service_name);//$this->instantiate($service_name);
    }


    private function instantiate($class_name, $type = "service")
    {

        try {
            $reflection = new \ReflectionClass($class_name);
        } catch (\ReflectionException $a) {
        //    throw new FatalException("Failed dependency load", "Unable to load dependency. "  . $a->getMessage() . " for class: ". $class_name);
            die("diee");
        }
        $constructor = $reflection->getConstructor();
        if (!$constructor) { // If there is no constructor - add simple object
            echo "bez constructora";
        }
        $dependencies = [];
        $params = $constructor->getParameters();
        //die("111");
        foreach ($params as $param) {
            try {
                $class = $param->getClass();
            } catch (\ReflectionException $a) {
              //  throw new FatalException("Failed dependency load", "Unable to load dependency. "
                //    . $a->getMessage() . " for class: ". $class_name);
                die("222");
            }

            if ($class) {
                echo "<br />Klasa zalezna: ". $class->name . "<br/>";
                if ($class->name == get_class($this)) { // if dependency is an instance of App\Bootstrap
                    //echo "ten";
                    $dependencies[] = $this;
                } elseif ($this->isBundle($class->getShortName())) { // dependency could be loaded before as a bundle
                    $dependencies[] = $this->getBundle($class->getShortName());
                } else {
                    $dependencies[]  = "adas" . $class->name;//$this->getService($class->name);
                    //die();
                }
            }
        }
//            echo "<br />".$class_name ."   Dependencies<pre>";
//            print_r($dependencies);
//            echo "</pre>";
        return $this->addService($reflection->newInstanceArgs($dependencies), $reflection->getShortName());

        return ;
        ;
        if (!$constructor) { // If there is no constructor - add simple object
            echo "bez constructora";
            if ($type == "service") {

                return $this->addService($reflection->newInstance(), $reflection->getShortName());
            }
            if ($type == "bundle") {
                return $this->bundles[$reflection->getShortName()] = $reflection->newInstance();//$this->addService($reflection->newInstance(), $reflection->getShortName());
            }
        }

        if ($type == "service") {
//            echo "Dependencies<pre>";
//            print_r($dependencies);
//            echo "</pre>";
        //    return $this->addService($reflection->newInstance(), $reflection->getShortName());
            return $this->addService($reflection->newInstanceArgs($dependencies), $reflection->getShortName());
        }
        if ($type == "bundle") {
            return $this->bundles[$reflection->getShortName()] = $reflection->newInstanceArgs($dependencies);//$this->addService($reflection->newInstance(), $reflection->getShortName());
        }
    }


    public function addService($object, $name = "")
    {
        //print_r($object);
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        } else {
            return $this->services[$name] = $object;
                //$this->instantiate($object, "service");//
        }
    }

    public function listServices()
    {
        return $this->services;
    }


//    public function registerService($service_name)
//    {
//        $service_class = "\App\\".$service_name;
//        if(class_exists($service_class) && !array_key_exists($service_name, $this->_services))
//        {
//           // $class = "\App\\".$service_name;
//            return $this->instantiate($service_class);
////
////            if(class_exists($class) && !array_key_exists($service_name, $this->_services)) {
////                $reflection = new \ReflectionClass($class);
////                $constructor = $reflection->getConstructor();
////                if (!$constructor) {
////                    return $reflection->newInstance();
////                }
////                $params = $constructor->getParameters();
////                foreach ($params as $param) {
////                    // Check for type hints in constructor
////                    $paramType = $param->getClass();
////                    if ($paramType) {
////                        // If there are type hints, call this very function
////                        // again (recursion) in order to fetch dependency
////                        $dependencies[] = $paramType;// $this->instantiate($paramType->name);
////                    }
////                }
////
////                echo "<pre>";
////                print_r($dependencies);
////                print_r($reflection->getConstructor()->getParameters());
////                //echo get_class($reflection->getConstructor()->getParameters()->name);
////                echo "</pre>";
////                echo "<pre>klasa istnieje" . $reflection->getConstructor();
//            //}
//        }
//
//        else{
//            throw new FatalException("Service", "Unable to get a service: $service_name");
//        }
//    }



    public function getDatabase()
    {
        if (isset($this->bundles["Database"])) {
            return $this->bundles["Database"];
        } else {
            $this->addBundle(new Database($this));
            return $this->getBundle("Database");
        }
    }
    
    
    public function delBundle($bundle)
    {
        if ($this->isBundle($bundle)) {
            unset($this->bundles[$bundle]);
        }
    }
    
    

    
    public function bundleList()
    {
        $list = array();
        foreach ($this->bundles as $key => $value) {
            $list[$key] = get_class($value);
        }
        return $list;
    }
    
    
    private function handlePage($page, Request $request)
    {
        $this->page = new App_Object($page);
        $this->page->add("request", $request);
        $this->page->prefered_lang = $request->getPreferedLanguage($request->languages);
        //  echo "<br />Jezyk strony w ktorym bedzie wyswietlana: $page->lang. Jezyk prefer
        //owany: " . $this->page->prefered_lang. "<br />";
        $controller_name = "\App\Controller\\" . $page->struct->get('controller');
        $controller = new $controller_name($this);
        return $controller->callControllerMethod($page->struct->get('method'));
    }

    
    
    public function handleRequest(Request $request)
    {
        if ($this->booted === false) {
            $this->bootUp();
        }
        // checking url
        try {
            $route = new Route();
            $this->lang = $request->languages;
            return $this->handlePage($route->checkUrl($request), $request);
        } catch (\Error $error) {
            return $this->HandleException($error);
        } catch (\ErrorException $e) {
            die("(*(*(*(");
        } catch (\Exception $exception) {
            return $this->HandleException($exception);
        }
      //  return new \Core\Http\Response\Response($this, ob_get_contents()); // tymczasowo zeby byla jakas tresc :)
    }
    
    
    public function getTpl()
    {

        return $this->container->getBundle("View");
    }
    

    
    public function handleException($exception)
    {
        echo "<pre>";
        print_r($exception);
        echo "</pre>";
        $temporary= new App_Array([
                "controller" => method_exists($exception, "getName") ? $exception->getName(): "Unknown name",
                "method" => __FUNCTION__,
                "prefered_lang" => isset($this->page->prefered_lang) ? $this->page->prefered_lang : "pl",
                "lang" => isset($this->page->lang) ? $this->page->lang : "pl"
            ]);
        $this->page = new App_Object(["struct" => $temporary]);
        if ($this->container->isBundle("View") === false) { // No templates system
            http_response_code(500);
            $content = "Fatal Error occured with message <b>". $exception->getMessage() . "</b>";
            $response = new Response($this, $content, 500);
            return $response;
        }
        $template = $this->getTpl();
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
                $template->assign("title", method_exists($exception, "getTitle") ? $exception->getTitle(): "Unknown title");
                $template->assign("name", method_exists($exception, "getName") ? $exception->getName(): "Unknown name");
                $template->assign("debug_info", method_exists($exception, "getDebug") ? $exception->getDebug() : "Unknown debug info");
            }
            $template->assign("message", method_exists($exception, "getMessage") ? $exception->getMessage(): "Unknown message");
            $template->assign("content", ob_get_contents());
            //ob_end_clean();
            $content = $template->fetch("Exception/fatal.tpl");
            $response = (new Response($this, $content, 500))->addHeader("aaaa");
            //$response
        }
        $response->sendResponse();
        die();
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
    
    

}
