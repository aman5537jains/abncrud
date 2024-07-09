<?php

namespace Aman5537jains\AbnCmsCRUD;

use Aman5537jains\AbnCmsCRUD\Components\TextComponent;
use stdClass;

abstract class Layout extends Component{

    public $results =[];
    public $processedResults =[];
    public $fields =[];
    public $chidBeforeRender;
    public $layoutType ="list";
    public $isBuild=false;
    public $model;
    public $template;


    function defaultComponent(){
        return TextComponent::class;
    }

    function setLayout($layout){
         $this->layoutType=$layout;
         return $this;
    }
    function setModel($model){
        $this->model=$model;

        return $this;
    }
    function getModel(){
       return  $this->model;
    }

    function getResults(){
        return $this->results;
    }
    function setResults($results){
          $this->results=$results;
          return $this;
    }
    function addField($name,$value=""){

        if($value instanceof Component){

            if(empty($value->getConfig("name","")) || $value->getConfig("name") == 'no_name_field')
            {
                $value->setConfig("name",$name);
                $value->setConfig("placeholder",$name);
            }
            $this->fields[$name]=$value;

        }
        else if(isset($value['class'])){
            $fieldConfig = isset($value['config'])?$value['config']:[];
            if(!isset($fieldConfig["name"])){
                $fieldConfig["name"] =$name;
            }
            $this->fields[$name]= new $value['class']($fieldConfig,$this);

        }
        else{
            // $fieldConfig = [];
            // $fieldConfig["name"] =$name;
            // $component = $this->defaultComponent();
            // $cmp = new $component($fieldConfig,$this);
            // $cmp->setValue("");
            // $this->fields[$name] = new $component($fieldConfig,$this);
        }

        return $this;
    }

    function removeAllFields(){
        $this->fields = [];
        return $this;
    }

    function onlyFields($arr){
        foreach($arr as $fld){
            if($this->hasField($fld))
            $newFilds[$fld] =$this->getField($fld);
        }
        $this->setFields($newFilds);
        return $this;
    }

    function modifyField($name,$callback){
        $this->addField( $name,$callback($this->fields[$name],$this));
        return $this;
    }

    function removeField($name){
        unset($this->fields[$name]);
        return $this;
    }

    function addFieldAfter($afterFieldName,$name,$options){
        $allFields = $this->getFields();
        $index = array_search($afterFieldName, array_keys($allFields));
        if ($index !== false) {
            $index++;

            $allFields=  array_splice($allFields, 0, $index, true)+
            array($name => $options) +
            array_slice($allFields, $index, count($allFields)-$index, true);

            $this->setFields($allFields);
        }
        else{
            $this->addField($name,$options);
        }

        return $this;
    }

    function addFieldBefore($beforeFieldName,$name,$options){
        $allFields = $this->getFields();
        $index = array_search($beforeFieldName, array_keys($allFields));
        if ($index !== false) {
            array_splice($allFields, $index, 0, array($name => $options));
            $this->setFields($allFields);
        }
        else{
            $this->addField($name,$options);
        }
        return $this;
    }
    function setField($field,$component){
        return $this->addField($field,$component);
    }
    function setFields($fields){
        return $this->fields=$fields;
    }

    function getFields(){

        return $this->fields;
    }


    function hasField($name){

        return isset($this->fields[$name]);
    }
    function getField($name){

        return $this->fields[$name];
    }


    function setValue($value){


        return parent::setValue($value);
    }

    public function processFields($rows,$viewFields,$type="list"){

        $allFields = [];
        $allRows= [];
        $fields =[];


        $fields = $this->getFields();

        foreach($rows as $k=>$row){

            $allRows[$k] = (object)[];

            foreach($fields as $field=>$value){

                if($value instanceof Component){
                    $cloned =clone $value;
                    if(empty($cloned->getConfig("name","")))
                    {
                        $cloned->setConfig("name",$field);
                    }
                    $allFields[$field]=$cloned;

                }
                else if(isset($value['class'])){
                    $fieldConfig = isset($value['config'])?$value['config']:[];
                    if(!isset($fieldConfig["name"])){
                        $fieldConfig["name"] =$field;
                    }
                    $allFields[$field] = new $value['class']($fieldConfig,$this);

                }
                else{
                    $fieldConfig = [];
                    $fieldConfig["name"] =$field;
                    $component = $this->defaultComponent();
                    $allFields[$field] = new $component($fieldConfig,$this);
                }

                if(isset($row->{$field})){

                    $allFields[$field]->setValue(@$row->{$field});
                }

                $allFields[$field]->setData(["row"=>$row]);

                // dd($allRows[$k]->{$field});

                $allRows[$k]->$field = $allFields[$field];
            }
        }

        // $this->fields = $allFields;
        $this->results = $allRows;
        return [$allFields,$allRows];

        }

        function setDefaultConfig($config)
        {
            if(isset($config["beforeRender"])){
                $chidBeforeRender =$config["beforeRender"];
                $config["beforeRender"]= function($component)use( $chidBeforeRender){
                    $chidBeforeRender();
                    $this->beforeRender($component);
                };
            }
            else{
                $config["beforeRender"]= function($component){
                    $this->beforeRender($component);
                };
            }

            parent::setDefaultConfig($config);
        }

        function beforeRender($component){
            $this->build();
        }
        function inputLayout(){
            if(!empty($this->template)){
                $fn = $this->template;
                $view = $fn( (object)$this->getFields(), $this->getResults(),$this);
                return $view;
             }
            return view($this->getConfig("input-layout","AbnCmsCrud::crud.input-layout"),[
              "rows"     => $this->getResults(),
              "fields"   => $this->getFields(),
              "component"=> $this
            ])->render();
        }
        function setTemplate($fn){

            $this->template =$fn;
        }


        function build($force=false){

            if($force==true || $this->isBuild==false){
                $this->processFields(empty($this->getValue())?[]:$this->getValue(),$this->getConfig("fields",null),$this->layoutType);
            }

            $this->isBuild=true;
            return $this;
        }








    }
