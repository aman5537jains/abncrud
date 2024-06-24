<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class MultiComponent extends ViewComponent{



    function view(){
        $rendered='';
        $componenets = $this->getConfig("components",[]);

        foreach($componenets as $componenet){
            $rendered.=$componenet->setValue($this->getValue())->setData($this->getData())->render();
        }

        return $rendered;
    }

}
