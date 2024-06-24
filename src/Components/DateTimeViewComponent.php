<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class DateTimeViewComponent extends ViewComponent{



    function format($format)
    {
        $parent=parent::getValue();
         return  date($format, strtotime($parent));

    }

    function view(){
        if($this->getValue() instanceof \Carbon\Carbon){
            return $this->getValue();
        }
        return  is_string($this->getValue())|| is_numeric($this->getValue())?$this->getValue():json_encode($this->getValue());
    }

}
