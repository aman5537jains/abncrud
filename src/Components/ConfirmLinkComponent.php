<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class ConfirmLinkComponent extends ViewComponent{

    function view(){
        return  "<a target='".$this->getConfig("target","")."' onClick='return confirm(\"Are you sure ?\");' href ='".$this->getConfig("link",$this->getValue())."'  > ".$this->getConfig("label","View")." </a>"  ;
    }

}
