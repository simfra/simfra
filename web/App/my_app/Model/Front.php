<?php
namespace my_app\Model;

//use App\Database\Database;
use App\Form\Form;

//use App\Form\FieldTypes\BaseType;
//use App\Form\FieldTypes\TextType;

class front extends \Core\Model
{

    public function index()
    {
        //echo "<pre>";
        //$this->getService("App\Database\Database");
        echo "AAA<pre>";
        //die();
        print_r($this->getService("Properties\Properties"));
        //print_r($this->getKernel()->bundleList());
        //print_r($this->getBundle("Debug"));

        //print_r($this->getContainer()->listBundles());
        echo "</pre>";
//        echo "aaa";
        //return $this->render("front/front.tpl");
        $n =$this->getKernel()->getDatabase();// $this->render("front/front.tpl");

        // tworzenie formularza w oparciu o dane konfiguracyjne przekazywane do konstruktora
        $form = new Form(["name" => "contact", "class" => "contact_form bootstrap", "action" => "/", "method" => "post", "submit" => "Wyślij"]);


        //$a = new \App\Form\FieldTypes\TextType;
        //$a->
        //var_dump($a);
        $form->addField("nazwa", "TextType", [
            "required" =>  true,
            "min-lenght" => 2,
            "max-lenght" => 30,
            "rule" => "alpha",
            "class" => "new",
            "autocomplete" => 1,
            "label" => "asdfsd"
        ]);
            $form->addField('nazwa2', "TextType", ["plus" =>  0, "id"=> "nowy"]);

            $form->process();
        //$form->getFields();

        //$form->loadXml("/Schema/contact.xml");
        //$form->saveFormToXml("new.xml");

        //echo "<pre>".print_r($form->getFields(), true)."</pre>";
        
        //echo "adas";
        //var_dump(function_exists(mb_strlen));
        //extension_loaded();
        $smarty = $this->getTemplete();
        //$a = new \Smarty();
        //$a->assignByRef()
        $smarty->assignByRef("fields", $form);
        //echo $form->fields[0]->generateView();
        return $smarty->fetch("front/front.tpl");//"<pre>".print_r($form->getFields(), true)."</pre>";//$a->generateView()."<pre>".print_r($form->getFields(),true)."sdfsd</pre>";//" print_r($form->fields,true);
        
        // generate content for response
//        echo "<pre>";
//        print_r($this->kernel->BundleList());
//        echo "</pre>";
        //echo isset($this) ?  $this->kernel->page : "aaa";
        //$this->kernel->getTpl()->fetch('string:'."afdsd");
        //$kernel = $this->getKernel();
        //$kernel->getTpl()->assign("aaa", $kernel->page->struct);
        //$kernel->getDatabase()->query("select * from aktywnosc limit 1");
        
        //$n = new \App\Database\Database($kernel);
        //$n->query("select * from inbox limit 1");
        //return $kernel->getTpl()->fetch("front/front.tpl");//.ob_get_contents();
    }
    
}