<?php
namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;

class RichEditor extends InputComponent{

    function __construct($config)
    {
            $config=["type"=>"textarea","attributes"=>["class"=>"rich-editor"]];
            parent::__construct($config);
    }



}
