<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts; 
class FormArray  extends FormBuilder
{   

    function setFormNameArray(){
        $formName = $this->getConfig("name");
        foreach ($this->getFields() as $key => $value) {
            $name = $value->getConfig("name");
            $value->addAttributes(["name"=>"$formName"."[".$name."]","data-validation-key"=>"$formName.$name"]);
        }
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
     

    function view(){
        $this->setFormNameArray();
        return parent::view();
    }
}
