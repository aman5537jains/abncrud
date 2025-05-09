<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts; 
class FormArrayInput  extends FormBuilder
{   

    function setFormNameArray(){
        $formName = $this->getConfig("name");
        foreach ($this->getFields() as $key => $value) {
            $name = $value->getConfig("name");
            $value->addAttributes(["name"=>$formName."[][".$name."]","data-validation-key"=>"$formName.$name"]);
        }
    }

    function onSaveModel($model){
     
        if($this->getConfig("saveMethod","JSON")=="JSON")
            $model->{$this->getConfig("name")} = json_encode($this->getValue());
        else if($this->getConfig("saveMethod","JSON")=="RELATIONAL"){
            $relation = $this->getConfig("relation");
            $modelClass = $model->{$relation}()->getRelated();
          
            $class = get_class($modelClass);
            $ids = [];  
            $class = new $class;
            foreach($this->getValue() as $key=>$value){
                foreach($value as $vals){
                    if($model->exists){
                        $classFind =  $class::where($model->{$relation}()->getForeignKeyName(),$model->{$model->{$relation}()->getLocalKeyName()})->where($key,$vals)->first();
                        if($classFind){
                            $class = $classFind;
                            $ids[] = $class->id;
                        }
                        else{
                            $class = new $class;
                        }
                    }
                   
                    $model->{$relation}[]= $this->getField($key)->setValue($vals)->onSaveModel($class);
                }

            }
          
            if(count($ids)>0){
                
                $model->{$relation}()->whereNotIn("id",$ids)->delete();
            }
            
           
        
        }
        return $model;
    }


    function setValue($values){
      
        try{
            $values= is_string($values)?json_decode($values,true):$values;
        }
        catch(\Exception $e){

        }
        $newVal = [];
        foreach($values as $key=>$val){
            foreach($val as $name=>$v){
                $newVal[$name][] = $v;
            }
             
        }
       
        return parent::setValue($newVal);
        
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
    function validationMessages()
    {
        $formName = $this->getConfig("name");
        $rules = parent::validationMessages();
    
        $newRule = [];
        foreach($rules as $key=>$rule){
            $newRule[$formName.".".$key] =$rule;
        }
        return $newRule;

        
    }

    function view(){
        $this->setFormNameArray();
        return parent::view();
    }
}
