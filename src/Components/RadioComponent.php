<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;

class RadioComponent extends FormComponent{



    function js(){


                return "

                ";

    }


    function view(){
        $name = $this->config["name"];
        $options = $this->getConfig("options",[]);


        $optionsRadio='';


        foreach($options as $key=>$value){
            $optionsRadio.="<li class='mb-3'>
            <label class='checkboxshow'>
                 ".\Form::radio($name, $key, $this->getValue(),['class'=>'account_type me-2',"required"=>$this->validations()->isRequired()])." $value </label>
                 </li>";


        }
        return "
        <ul>
        <li class='mb-3'>
            <label class='dForm-label'> ".$this->getLabel()."  ".$this->requiredSpan()."</label>
        </li>
        ".
                 $optionsRadio
                 ."
        </ul>

        ";

        // return "<input type='$type' name='$name' placeholder='$placeholder' />";
    }

}
