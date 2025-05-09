<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

 
use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class HtmlFormComponent extends FormComponent{
   
    function onSaveModel($model){

        $model->{$this->getConfig("name","")} = json_encode($this->getValue());
        return $model;
    }
    function view(){
       
        return $this->view;
    }

}
