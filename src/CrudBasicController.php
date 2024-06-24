<?php

namespace Aman5537jains\AbnCmsCRUD;

class CrudBasicController extends CrudController
{
    public $formFieldHandler;
    public $formViewHandler;

    function setFormFields($cb){
        $this->formFieldHandler= $cb;
        return $this;
    }

    function setViewFields($cb){
        $this->formViewHandler= $cb;
        return $this;
    }

    public function viewFields($builder){
        $fields  = parent::viewFields($builder);
        if($this->formViewHandler){
            $cb = $this->formViewHandler;
            $cb($builder);
        }
    }

    function formFields($builder){
        $fields  = parent::formFields($builder);
        if($this->formFieldHandler){
            $cb = $this->formFieldHandler;
            $cb($builder);
        }
    }
}
