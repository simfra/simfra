<?php
    /**
     * Created by PhpStorm.
     * User: polo
     * Date: 27.11.17
     * Time: 19:37
     */

class Controller
{
    private $dzwiek = "default";

    public function __construct($dzwiek)
    {
        //var_dump(parent::__construct);
        $this->dzwiek = $dzwiek;
        echo "<br />Controler (" .$dzwiek .") <br />";
    }

    public function szczekaj()
    {
        return "<br />Szczeka ". __CLASS__. " " .$this->dzwiek ."<br/>";
    }
}