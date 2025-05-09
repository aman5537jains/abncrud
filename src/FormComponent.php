<?php

namespace Aman5537jains\AbnCmsCRUD;



use Aman5537jains\AbnCmsCRUD\Traits\AjaxAttributes;
use Illuminate\Support\Facades\Validator;
abstract class FormComponent extends Component{
    use AjaxAttributes;
    private $__validations;
    private $__clasess='';

    function getAttributes(){
        $name           = $this->getConfig("name");
        $id             = $this->getConfig("id",$name);
        $validations    = $this->validator();
        $required       = $validations->isRequired()?['required'=>true]:[];
       
        $inputClass     = $this->getConfig("input-class","dForm-control")." ".$this->__clasess;
        $placeholder    = $this->getConfig("placeholder",$this->getConfig("label",""));
        return $required+ ['placeholder' => $placeholder, 'class'=>$inputClass,"id"=>$id]+$this->getConfig("attr",[]);
    }
    function getAttribute($name,$default=""){
        $atrr = $this->getAttributes();
        return isset($atrr[$name])?$atrr[$name]:$default;
    }

    function addClass($classes){
        $this->__clasess.= $classes;
        return $this;
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
    function validationMessages(){
        return $this->__validations->messages;
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
    function setValidations($validations,$messages=[]){
        $this->__validations = new LaravelInputValidations($validations,$messages);
        return $this;
    }


    function setDefaultConfig($config)
    {
        parent::setDefaultConfig($config); 
        $this->setValidations($this->getConfig("validations",[]),$this->getConfig("validation_messages",[]));


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
        $this->ajaxAttrSetup();
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

class LaravelInputValidations{
    public $validations;
    public $messages=[];
    public $isRequired=false;
    function __construct($validations,$messages)
    {
        // $this->validations = $validations;
        $this->messages= $messages;
        $this->validations=$validations;

    }
    function getValidations(){
        return $this->validations;
    }
     function isRequired(){
        $validator= Validator::make([], ["name"=>$this->validations]);
        $ruleSet = $validator->getRules();
        return in_array('required', $ruleSet['name']);
         
    }
    function isValid(){
        return isset($this->validations['required']);
    }
}
class InputValidations{
    public $validations=[];
    public $messages=[];
    public $isRequired=false;
    function __construct($validations,$messages)
    {
        // $this->validations = $validations;
        $this->messages= $messages;
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
