<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts; 
class FormGroup  extends FormBuilder
{   
    function view(){
         
        return "<div style='   border: 1px solid; padding: 10px; border-radius: 10px; margin: 13px 0;'><h2>".$this->getLabel()."</h2>".parent::view()."</div>";
    }
}
