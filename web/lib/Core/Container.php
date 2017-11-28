<?php

    namespace lib\Core;


class Container {
    private $bundles = [];
    private $services = [];

    public function __construct()
    {
        return $this;
    }

    public function getBundle($name)
    {
        if (array_key_exists($name, $this->bundles)) {
            return $this->bundles[$name];
        } else {
            throw new FatalException("Bundle", "No bundles with specified name ($name)");
        }
    }

    public function addBundle($bundle, $name = "")
    {
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
    }

    public function isBundle($bundle)
    {
        return isset($this->bundles[$bundle]) ? true : false;
    }


    public function listBundles()
    {
        return $this->bundles;
    }




}