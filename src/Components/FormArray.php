<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class FormArray extends ViewComponent{

    function view(){
        //  $relation  = $this->getConfig("relation");


        return $this->getValue();
    }

}
