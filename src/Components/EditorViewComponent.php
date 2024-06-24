<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class EditorViewComponent extends ViewComponent{



    function short($limit=100)
    {
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $this->getValue());

        // Remove <style> and </style> tags and their content
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $this->getValue());

        // Remove all HTML tags

        $html = strip_tags($html);
        if(strlen($html)<=$limit){
            return substr($html,0,$limit);
        }
        else{
            return substr($html,0,$limit)."...";
        }

    }

    function view(){

        return  is_string($this->getValue())|| is_numeric($this->getValue())?$this->getValue():json_encode($this->getValue());
    }

}
