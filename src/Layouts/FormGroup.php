<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts; 
class FormGroup  extends FormBuilder
{   
     function addField($name, $value = ''){
        $formName = $this->getConfig("name");
        $name = $value->getConfig("name");
        $value->addAttributes(["name"=>"$formName"."[".$name."]","data-validation-key"=>"$formName.$name"]);
        return parent::addField($name,$value);
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

    function view(){
         $name = $this->getConfig("name");
        return '<div style="    border: 1px solid;
                padding: 10px;
                border-radius: 10px;
                margin: 13px 0;" class="repeatable-container" >
                        <h2>'.$this->getLabel().'</h2>
                        <div class="form-group-forms-'.$name.'">'.parent::view().'
                        </div>
              </div>';
    }
 
}
