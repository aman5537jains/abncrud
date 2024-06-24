<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
class InputComponent extends FormComponent{

    function defaultConfig(){
        return [
            "type"=>"text",
            "options"=>[
                "text"      => "Text",
                "textarea"  => "Textarea",
                "number"    => "Number",
                "password"  => "Password",
                "email"     => "Email",
                "date"      => "Date",
                "datetime"  => "Date Time",
                "color"     => "Color",
                "hidden"    => "Hidden",
                "select"    => "Select",
                "radio"     => "Radio"
            ],
            "multiple"=>false,
        ];
    }



    function configComponents()
    {
        $addMore = new AddMoreComponent(["name"=>"options"]);
        $addMore->addField("key",new InputComponent(["name"=>"key"]));
        $addMore->addField("value",new InputComponent(["name"=>"value"]));

        return [
            "type"=>new InputComponent(["name"=>"type","value"=>"text","type"=>"select","options"=>$this->getConfig("options")]),
            "options"=>$addMore,
            "multiple"=>new InputComponent(["name"=>"multiple","value"=>"0","type"=>"select","options"=>["0"=>"No","1"=>"Yes"]])
        ];
    }
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
            // $attr = $this->getConfig("attr",[]);

            $optionattr  = $this->getConfig("options-attr",[]);
            $values = [$this->getValue()];
            if($this->getConfig("multiple",false)){
                unset($attrs["placeholder"]);
                $attrs  =  $attrs + ["data-placeholder"=>$placeholder];

                $values = $this->getValue();

            }
            $str='';
            foreach($attrs as $a=>$v){
                 $str .="$a=\"$v\"";
            }

            $select  = "<select $str name=\"$name\"    >";
            $select.="<option   value=''>Select</option>";

            foreach($options as $key=>$option){

                $stro="";
                if(isset($optionattr[$key])){
                        foreach($optionattr[$key] as $atr_name=>$val){
                            $stro .= "$atr_name='$val'";
                        }

                }
                if(!empty($values))
                    $selected = in_array($key,$values)?"selected":"";
                else
                    $selected ='';
                $select.="<option $selected $stro value='$key'>$option</option>";
            }

            $select.='</select>';
            // $optionattr
            $input=$select;//\Form::select($name,$options,$this->getValue(), $attrs);
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
            $this->setConfig("showLabel",false);
            $attrs['data-config']=$this->getValue();
            $input =\Form::hidden($name,$this->getValue(), $attrs);
        }

        else{

            $input =\Form::text($name,$this->getValue(), $attrs);
        }
        return $input;

    }



}
