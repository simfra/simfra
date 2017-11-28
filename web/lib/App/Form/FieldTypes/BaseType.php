<?php
namespace App\Form\FieldTypes;

abstract class BaseType
{
    private $name = "";
    private $options = [];
    private $type = "";
    private $value = "";
    
    public function __construct()
    {
        $this->setDefaults([
            "required" => false,
            "rule" => "alpha",
            "class" => "",
            "class_valid" => "",
            "class_error" => "",
            "label" => "",
            "placeholder" => "",
        ]);
        $this->type = "text";
    }
    
    public function setDefaults($defaults)
    {
        foreach ($defaults as $key => $value) {
            $this->options[$key] = $value;
        }
        return $this;
    }

    public function unsetOption($key)
    {
        if (array_key_exists($key, $this->options)) {
            unset($this->options[$key]);
        }
        return $this;
    }
    
    public function getOption($key)
    {

        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
    }

    
    public function generateView()
    {
        //echo var_dump($this->getOption("autocomplete"));
        $temp = "<input name=\"{$this->name}\" value=\"{$this->value}\"";
            (!empty($this->type)) ? $temp .= " type=\"{$this->type}\"" : "";
            ($this->getOption("id")) ? $temp .=" id=\"{$this->getOption("id")}\"" : "";
            ($this->getOption("class")) ? $temp .=" class=\"{$this->getOption("class")}\"" : "";
            ($this->getOption("max-lenght")) ? $temp .=" maxlenght=\"{$this->getOption("max-lenght")}\"" : "";
            ($this->getOption("disabled")) ? $temp .=' disabled': '';
            /// Just HTML5
            ($this->getOption("autocomplete")) ? $temp .=' autocomplete="on"': '';
            ($this->getOption("required")) ? $temp .=' required="required"': '';
            ($this->getOption("autofocus")) ? $temp .=' autofocus': '';
            $temp .= " >";
        return $temp;
    }
    
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setOption($key, $value)
    {
        if (trim($value) !== "") {
            $this->options[$key] = $value;
        }
        return $this;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    
    
}