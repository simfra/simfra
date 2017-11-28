<?php
namespace Core\Config;



use \Core\Exception\FatalException;
use lib\Core\Bundle;

class Config extends Bundle
{
    private $_config;
    
    public function __construct()
    {
        //echo "construct";
//        if($settings != "") {
//            $this->_config = $settings;
//        }
        $this->loadConfigFromIni();
        return $this;
    }
    
    public function get($variable)
    {
        if (isset($this->_config[$variable])) {
            return $this->_config[$variable];
        }
    }

    public function getConfig()
    {
        return $this->_config;
    }


    public function loadConfigFromIni()
    {
        $plik = $_SERVER['DOCUMENT_ROOT'] ."/../App/Config/config.php";
        if (file_exists($plik)) {
            if (is_readable($plik)) {
                //file
                //print_r( $output = shell_exec('php -l "'.$plik.'"'));
                //@TODO zrobic bezpieczne wczytywanie plikow. http://www.php.net/manual/en/function.php-check-syntax.php
                $t = include($plik);
                //$t = file_get_contents($plik);
                //$this->_config[] = parse_ini_file($plik, true, INI_SCANNER_TYPED);
//echo "<pre>iiii";
//print_r($t);
//echo "</pre>";
                if (is_array($t)) {
                    $this->_config = $t;
                }
//                echo "<pre>111";
//                print_r($this->_config);//['bundles']);//json_decode($t, true));
//                echo "</pre>";
            } else {
                die("File: ".$plik . " isn't readable");
            }
        } else {
            die("File: ".$plik . " doesn't exist");
        }
        return;
//        
//        foreach($file as $line)
//        {
//            if(!preg_match('/^\/|^\/\/|^#|^\ /i', $line)) // Skip lines with single /, double // or # - comments
//            {
//                if(trim($line)!="") {
//                    $temp = explode("=",$line);
//                    if(count($temp)==2)
//                    {
//                        $this->_config[trim($temp[0])] = eval(trim($temp[1]));
//                        //echo "<pre>";
//                        //print_r($temp);
//                        //echo "</pre>";
//                    }
//                    else{
//                        echo "bledna wartosc " .$line . "<br />";
//                    }
//                }
//                //echo $line."<br />";
//            }
//        }        
//        //echo "<pre>";
        //print_r($this->_config);
        //echo "</pre>";
    }
}