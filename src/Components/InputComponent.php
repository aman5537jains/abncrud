<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
class InputComponent extends FormComponent{

    function buildInput($name,$attrs){

        $type           = $this->getConfig("type","text");
        $options        = $this->getConfig("options",[]);
        $placeholder = $this->getConfig("placeholder",$this->getConfig("label",""));

        if($type=="textarea"){
            $input= \Form::textarea($name,$this->getValue(),$attrs);
        }
        else if($type=="password"){
            $input= \Form::password($name,$attrs);
        }
        else if($type=="email"){
            $input= \Form::email($name,$this->getValue(),$attrs);
        }

        else if($type=="number"){
            $input= \Form::number($name,$this->getValue(),$attrs);
        }
        else if($type=="date"){
            $input= \Form::date($name,$this->getValue(), $attrs);
        }
        else if($type=="datetime"){

            $input= \Form::datetimeLocal($name,$this->getValue(), $attrs);
        }
        else if($type=="select"){
            $attr  = $this->getConfig("attr",[]);
            $optionattr  = $this->getConfig("options-attr",[]);
            if($this->getConfig("multiple",false)){
                unset($attrs["placeholder"]);
                $attrs  =  $attrs + ["data-placeholder"=>$placeholder];


            }

            $input= \Form::select($name,$options,$this->getValue(), $attrs,$optionattr);
        }
        else if($type=="radio"){
            $radio ="";
            foreach($options as $k=>$option)
            $radio  .=  \Form::radio($name,$k, false,$attrs) . " $option";
            $input= $radio;
        }
        else if($type=="color"){

            $input =\Form::color($name,$this->getValue(), $attrs);
        }
        else if($type=="hidden"){

            $input =\Form::hidden($name,$this->getValue(), $attrs);
        }

        else{

            $input =\Form::text($name,$this->getValue(), $attrs);
        }
        return $input;

    }



}
