<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts; 
class FormArray  extends FormBuilder
{   
    function addField($name, $value = ''){
          $formName = $this->getConfig("name");
           $name = $value->getConfig("name");
           $value->addAttributes(["name"=>"$formName"."[".$name."]","data-validation-key"=>"$formName.$name"]);
           parent::addField($name,$value);
    }
    
    function onSaveModel($model){
        $model->{$this->getConfig("name")} = json_encode($this->getValue());
        return $model;
    }
    function validations(){
        $formName = $this->getConfig("name");
        $rules = parent::validations();
        $newRule = [];
        foreach($rules as $key=>$rule){
            $newRule[$formName.".".$key] =$rule;
        }
        return $newRule;
    }

    function setValue($values){
       
        try{
            $values= is_string($values)?json_decode($values,true):$values;
           
        }
        catch(\Exception $e){

        }
        return parent::setValue($values);
    }
     

    
}
