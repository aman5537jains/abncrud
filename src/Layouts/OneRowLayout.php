<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts;

use Aman5537jains\AbnCmsCRUD\Component;
use Aman5537jains\AbnCmsCRUD\Components\InputComponent;
use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Layout;
use Illuminate\Support\Facades\Validator;
class OneRowLayout  extends Layout
{
    function init(){
        $this->setLayout("one");
        $this->setValue([]);

    }

    function setValue($value)
    {
        foreach($this->getFields() as $field=>$val){
            if(isset($value[$field])){
               $val->setValue($value[$field])->setData(["row"=>$value]);
           }
       }
       return parent::setValue([$value]);
    }
    function getValue()
    {
        return parent::getValue()[0];
    }
    function view(){
        return parent::view();
   }
}

