<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\Traits\AjaxAttributes;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class LinkComponent extends ViewComponent{
    use AjaxAttributes;

 
    function view(){
         $this->ajaxAttrSetup();
        return  "<a  ".$this->getAttributesString()." target='".$this->getConfig("target","")."' href ='".$this->getConfig("href",$this->getValue())."'  > ".$this->getConfig("label","View")." </a>"  ;
    }

}
