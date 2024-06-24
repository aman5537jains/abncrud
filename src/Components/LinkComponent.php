<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class LinkComponent extends ViewComponent{

    function view(){

        return  "<a target='".$this->getConfig("target","")."' href ='".$this->getConfig("link",$this->getValue())."'  > ".$this->getConfig("label","View")." </a>"  ;
    }

}
