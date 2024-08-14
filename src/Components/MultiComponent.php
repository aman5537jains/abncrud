<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class MultiComponent extends ViewComponent{

    public $components=[];

    public function init(){
        parent::init();
        $this->components = $this->getConfig("components",[]);
    }

    function renderComponents(){
        $rendered = '';
        $this->components = $this->getConfig("components",[]);
        foreach($this->components as $componenet){

            $rendered.=$componenet->setValue($this->getValue())
            ->setData($this->getData()
            )->render();
        }

        return $rendered;
    }

    function view(){
        return $this->renderComponents();
    }



}
