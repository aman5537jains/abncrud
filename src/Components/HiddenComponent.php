<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
class HiddenComponent extends FormComponent{


   
    function init(){
        $this->setConfig("col","aa");
    }
     function view(){
        
       
        $name = $this->getAttribute("name",$this->getConfig("name"));
        $val = $this->getValue();
        return "<input type='hidden' name='$name' value='$val' />";
     }



}
