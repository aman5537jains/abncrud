<?php

namespace Aman5537jains\AbnCmsCRUD;

use Illuminate\Support\Facades\Validator;
abstract class FormComponent extends Component{

    private $__validations;

    function getAttributes(){
        $name           = $this->getConfig("name");
        $id             = $this->getConfig("id",$name);
        $validations    = $this->validator();
        $required       = $validations->isRequired();
        $inputClass     = $this->getConfig("input-class","dForm-control");
        $placeholder    = $this->getConfig("placeholder",$this->getConfig("label",""));
        return  ['placeholder' => $placeholder, 'required'=>$required, 'class'=>$inputClass,"id"=>$id]+$this->getConfig("attr",[]);
    }
    function setAttributes($arr){
        $this->setConfig("attr",$arr);
        return  $this;
    }

    function buildInput($name,$attrs){
        return  \Form::text($name,$this->getValue(), $attrs);;
    }

    function validator(){
        return $this->__validations;
    }
    function validations(){
         return $this->__validations->getValidations();
    }
    function validate(){
        $validator =  Validator::make([$this->getConfig("name","none")=>$this->getValue()],[$this->getConfig("name","none")=>$this->validations()]);
         if ($validator->fails())
         {

            return $validator->errors();

         }
        return [];
   }




    function requiredSpan(){

      return $this->__validations->isRequired()?  "<span class='mandatory'>*</span>":"";

    }
    function setValidations($validations){
        $this->__validations = new InputValidations($validations);
    }


    function setDefaultConfig($config)
    {
        parent::setDefaultConfig($config);
        $this->setValidations($this->getConfig("validations",[]));


        $value =   request()->get($this->getConfig("name",""),"");
        if(!empty($value )){
            // $this->setValue($value);
        }
    }

    function onSave($value){
        if(!empty($value)){
            return $value;
        }
        else{
            return request()->get($this->config["name"],"");
        }
    }
    function onSaveModel($model){

        $model->{$this->getConfig("name","")} = $this->getValue();
        return $model;
    }

    function view(){

        $class          = $this->getConfig("parentClass","dForm-group");
        $labelClass     = $this->getConfig("label-class","dForm-label");
        $name           = $this->getConfig("name");
        $validations = $this->validations();


        // if( ){

        // }
        $input          = $this->buildInput($name,$this->getAttributes());
        if($this->getConfig("showLabel",true)){
            return '<div class="'.$class.'">
            <label class="'.$labelClass.'">'.$this->getLabel().' '.$this->requiredSpan().'</label>
                '.$input.'
            </div>';
        }
        else{
            return '<div class="'.$class.'">
                '.$input.'
            </div>';
        }


    }




}

class InputValidations{
    public $validations=[];
    public $isRequired=false;
    function __construct($validations)
    {
        // $this->validations = $validations;
        $this->process($validations);

    }
    function getJsValidations(){
        return $this->validations;
    }
    function getValidations(){
        return $this->validations;
    }

    function process($validations,$add=true){
        foreach($validations as $key=>$value){
                if($add){
                    $this->validations[$key]=$value;
                    if($value=="required"){
                     $this->isRequired=true;
                    }
                }
                else{

                    if(isset($this->validations[$key])){
                        unset($this->validations[$key]);

                        if($value=="required"){
                            $this->isRequired=false;
                        }
                    }
                }



            }


    }
    function remove($validations){
        $this->process($validations,false);
        return $this;
    }
    function removeAll(){
        $this->isRequired=false;
        $this->validations=[];
        return $this;
    }
    function add($validations){

        $this->process($validations);
        return $this;
    }

    function isRequired(){
        return  $this->isRequired;
    }
    function isValid(){
        return isset($this->validations['required']);
    }

}
