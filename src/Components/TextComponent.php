<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class TextComponent extends ViewComponent{


    function js(){

        return "";
    }
    function getValue()
    {
        $parent=parent::getValue();
        $formatted  = $this->getConfig("formatted",null);

        if($formatted!=null){

           return $formatted($parent,$this->getData()["row"]);
        }
        return $parent;
    }

    function view(){
        if($this->getValue() instanceof \Carbon\Carbon){
            return $this->getValue();
        }
        return  is_string($this->getValue())|| is_numeric($this->getValue())?$this->getValue():json_encode($this->getValue());
    }

}
