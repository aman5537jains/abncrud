<?php

namespace Aman5537jains\AbnCmsCRUD;

use Aman5537jains\AbnCmsCRUD\Components\ConfigBuilderComponent;

abstract class Component{
    public $config;
    public $value;
    public $data;
    public $componentInlineScript;
    public $controller;
    public $view;
    public $js='';
    public $CID;
    public static $counter=0;
    private $__clasess='';
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


     function getAttributes(){
         $name           = $this->getConfig("name");
         $id             = $this->getConfig("id",$name);


         $inputClass     = $this->getConfig("input-class","dForm-control")." ".$this->__clasess;
         $placeholder    = $this->getConfig("placeholder",$this->getConfig("label",""));
         return  array_merge(['placeholder' => $placeholder, 'class'=>$inputClass,"id"=>$id,"name"=>$name],$this->getConfig("attr",[]));
     }
     function getAttributesString(){
         $atrr = $this->getAttributes();
         $str="";
         foreach($atrr as $k=>$val){
             $str.="$k=\"$val\" ";
         }

         return $str;
     }
     function getAttribute($name){
         $atrr = $this->getAttributes();
         return isset($atrr[$name])?$atrr[$name]:"";
     }
     function hasAttribute($name){
        $atrr = $this->getAttributes();
        return isset($atrr[$name]);
    }


     function addClass($classes){
         $this->__clasess.= $classes;
         return $this;
     }
     function setAttributes($arr){
         $this->setConfig("attr",$arr);
         return  $this;
     }
     function addAttributes($arr){
         $attr = $this->getConfig("attr",[]);
         $this->setConfig("attr", array_merge($attr,$arr));
         return  $this;
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

        $rendered  =$this->render();
        $jsAll = [];
        foreach(CrudService::$allJs as $js=>$value){
            $jsAll[str_replace("\\","",$js)]=$value["js"]."<script>".$value['alpine']."</script>";
        }
        return [

            "html"=>$rendered,
            "js"=>$jsAll
        ];
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
    function componentJs(){
        $cname = $this->componentName();
        $id    = $this->componentID();
        return "crudBuilderJS.component('$cname','$id')";
    }

    function registerJsComponent(){
        $cmp = $this->componentName();
        return false;
    }
    function jsConfig(){
        return [];
    }

    abstract function view();

    function parentContainer($view,$jsComponent){

        return "<div ".$jsComponent." >". $view . "</div>";
    }

    function render(){
        try{


            $beforeRender = $this->getConfig("beforeRender",null);
            if($beforeRender){
                $beforeRender($this);
            }

            $js  = $this->getConfig("js","");
            $cName= $this->componentName();
            $cNameTrimed =  str_replace("\\","",$cName);
            $jsOnce='';
            $scripFnName= '';
            $componentID = $this->componentID();
            $scripFnName ='';
            $scripFnNameClose ='';
            $JSALPINE ="window['crudBuilderJS'].alpines['$cNameTrimed']=1;";
            if($this->registerJsComponent()){
                $object = $this->registerJsComponent();

                $JSALPINE= "
                        window['crudBuilderJS'].alpines['$cNameTrimed']=1;
                        Alpine.data('$cNameTrimed', (config={}) => {

                             return $object;

                        });

                ";
                // $jsOnce = "<script>crudBuilderJS.register(\"$cName\",".$this->registerJsComponent().",'$componentID');</script>";
                // $jsOnce = "<script>$JSALPINE</script>";

                // $encoded = json_encode($this->jsConfig());

                if(is_string($this->jsConfig())){
                    $encodedExtended =$this->jsConfig();
                }
                else{
                    $encodedExtended = "{";
                    foreach($this->jsConfig() as $key=>$val){
                        if($encodedExtended!="{"){
                            $encodedExtended.=",";
                        }
                        if(is_string($val))
                        {

                            $val = '"'.addslashes($val).'"' ;
                        }
                        else if(is_array($val)){
                            $val = json_encode($val);
                        }
                        else if(is_bool($val)){
                            $val = $val?"true":"false";
                        }
                        else if(is_callable($val)){
                            $val= $val();
                        }

                        $encodedExtended.=$key.":".$val;
                    }
                    $encodedExtended .= "}";
                    $encodedExtended=htmlspecialchars($encodedExtended);

                }


                // $jsConfig= base64_encode($encoded);

                // $scripFnName = "<img style='display:none' id='$componentID' src onError='crudBuilderJS.call(\"$cName\",this.nextSibling,\"$jsConfig\",\"$componentID\")' />";

                $scripFnName = " x-data='$cNameTrimed($encodedExtended)' ";


            }
            // else
            // $jsOnce = "<script>crudBuilderJS.register(\"$cName\",".$this->registerJsComponent().",'$componentID');</script>";
            CrudService::registerJs($this->componentName(),$js.$jsOnce ."". $this->js(),$JSALPINE,$componentID);
            $this->componentInlineScript = $scripFnName;
            // $scripFnName='';
            // if($this->scriptFunctionName()!=''){

                 // }


            $this->view =  $this->parentContainer($this->view(),$this->componentInlineScript);

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
