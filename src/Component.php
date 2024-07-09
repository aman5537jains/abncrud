<?php

namespace Aman5537jains\AbnCmsCRUD;

use Aman5537jains\AbnCmsCRUD\Components\ConfigBuilderComponent;

abstract class Component{
    public $config;
    public $value;
    public $data;
    public $controller;
    public $view;
    public $js='';
    public $CID;
    public static $counter=0;
     function __construct($config,$controller=null)
     {
        $this->controller =$controller;
        $this->setComponentID($this->generateComponnetUID());

        $this->setDefaultConfig( array_merge($this->defaultConfig(),$config) );
        $this->init();
     }

     function  generateComponnetUID(){
        Component::$counter =  Component::$counter+1;
        return "cmp_".Component::$counter;
     }

     function  setComponentID($id){
         $this->CID = $id;
     }

     function isVisible(){
            return $this->getConfig("visible",true);;
     }
     function setVisible($value){
         $this->setConfig("visible",$value);
         return $this;
     }

     function validations(){
        return  $this->getConfig("validations",[]);
     }

     function setValidations($rules){
        return  $this->setConfig("validations",$rules);
     }
     function addValidations(array $newRules){
        $validations = $this->validations();
        return  $this->setConfig("validations",array_merge($validations,$newRules));
     }
     public function init(){
        $init = $this->getConfig("onInit",null);
        if($init){
            $init($this);
        }
     }

     public function setValue($value){
            $this->value =$value;
            return $this;
     }

     public function componentID(){
        return $this->CID;
     }
     public function setData($value){
        $this->data =$value;

        return $this;
    }
    public function getData($key='',$default=''){
        if($key!=''){
            return isset($this->data[$key]) ? $this->data[$key]:$default;
        }
        return $this->data;
    }
    function setJs($js){
         $this->js = $js;
         return $this;
    }
    function js(){
        return $this->js;
    }
     public function getValue(){
        return $this->value === null || $this->value=== '' ?"":$this->value ;
     }
     public function getLabel(){
        return $this->getConfig("label");
     }
     public function setLabel($label){
          return $this->setConfig("label",$label) ;
     }
     function defaultConfig()
     {
       return [];
     }
     function configBuilder()
     {
       return ConfigBuilderComponent::class;
     }
     function configComponents()
     {
       return [];
     }

    function setDefaultConfig($config)
    {
        $this->config = $config;
        $this->setConfig("name",$this->getConfig("name","no_name_field"));
        $label= ucfirst(str_replace("_"," ",str_replace("_id"," ",$this->getConfig("name"))));

        $this->setConfig("label",$this->getConfig("label",$label));
        if($this->getConfig("value","")!=''){
            $this->setValue($this->getConfig("value",""));
        }
    }

    public function getConfig($name,$default=''){
        if(isset($this->config[$name]) )
            return $this->config[$name];
        else
            return  $default;
    }
    public function setConfig($name,$default=''){
             $this->config[$name]=$default;
             return $this;
    }
    public function setConfigs($arr){
        $this->config = array_merge($this->config,$arr );
        return $this;
}
    public function registerRoute($name,$cb){
        // CrudService::subscribe("registerRoute",function(){

        // });
    }
    function loadView($path,$arr=[]){
        try{
             $reflector = new \ReflectionClass(get_class($this));
             view()->addNamespace($reflector->getNamespaceName(), dirname($reflector->getFileName()));

            return view($reflector->getNamespaceName()."::$path",$arr)->render();
        }
        catch(\Exception $e){
              dd($e->getMessage()." at ". $e->getLine()." file ". $e->getFile());
        }

    }
    function withJs(){

        return $this;
    }
    function json(){

    }

    function setView($html){
        $this->view = $html;
        return $this;
    }
    function scriptFunctionName(){

        return "";
    }
    function componentName(){

        return $this->getConfig("componentName",get_class($this));
    }
    function registerJsComponent(){
        $cmp = $this->componentName();
        return false;
    }
    function jsConfig(){
        return [];
    }

    abstract function view();


    function render(){
        try{


            $beforeRender = $this->getConfig("beforeRender",null);
            if($beforeRender){
                $beforeRender($this);
            }

            $js  = $this->getConfig("js","");
            $cName= $this->componentName();
            $jsOnce='';
            $scripFnName= '';
            $componentID = $this->componentID();
            if($this->registerJsComponent()){

                $jsOnce = "<script>crudBuilderJS.register(\"$cName\",".$this->registerJsComponent().");</script>";
                $jsConfig= base64_encode(json_encode($this->jsConfig()));
                $scripFnName = "<img style='display:none' id='$componentID' src onError='crudBuilderJS.call(\"$cName\",this.nextSibling,\"$jsConfig\",\"$componentID\")' />";

            }

            CrudService::registerJs($this->componentName(),$js.$jsOnce ."". $this->js());

            // $scripFnName='';
            // if($this->scriptFunctionName()!=''){

                 // }


            $this->view =  "$scripFnName".$this->view()."";

            $init = $this->getConfig("afterRender",null);
            if($init){
                $init($this);
            }
            return $this->view;
        }
        catch(\Exception $e){
            dd($e);
           return "There is some error in Component";//
        }
    }

    function __toString()
    {
        return $this->render();
    }

}
