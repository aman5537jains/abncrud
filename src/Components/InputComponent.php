<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Traits\AjaxAttributes;
class InputComponent extends FormComponent{
    
    function registerJsComponent(){ 
        // if($this->getConfig("type")=="select")
        {
            return "function(component,config){
                 $(component).find('select').select2();
             }";
        }
         
    }
    
    function getRelationalOptions(){
        $relation        = $this->getConfig("relation",false);
       
        if( $relation){  
            if($this->controller){
                
                $model = $this->controller->getModel();
                
                if(is_string($relation)){
                    $modelClass = $model->{$relation}()->getRelated();
                   
                    $query = function($q){
                        return $q->pluck("name","id");
                    };
                }
                else{
                    $rname = $relation["name"];
                    $modelClass = $model->{$rname}()->getRelated();
                    if(isset($relation["query"])){
                        $query =$relation["query"];
                    }
                    else{
                        $query = function($q)use($relation){
                           return $q->pluck($this->getOption($relation,"titleKey","name"),$this->getOption($relation,"idKey","id"));
                        };
                    } 
                    
                }
               
                $class = get_class($modelClass);
                
                return $query($class::query());

            }
        }
        return [];
    }
    function buildInput($name,$attrs){
         

        $type           = $this->getConfig("type","text");
        $options        = $this->getConfig("options",$this->getRelationalOptions());
        
        $placeholder = $this->getConfig("placeholder",$this->getConfig("label",""));

        if($type=="textarea"){
           
            $input= \Form::textarea($name,$this->getValue(),$attrs);
        }
        else if($type=="password"){
            $input= \Form::password($name,$attrs);
        }
        else if($type=="url"){
            $input= \Form::url($name,$this->getValue(),$attrs);
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
            if($this->getConfig("multiple",false)){
                unset($attrs["placeholder"]);
                $attrs  =  $attrs + ["data-placeholder"=>$placeholder];
            }
            $str='';
            if(isset($attrs['class'])){
                $attrs['class'].=" select2-input";
            }
            else{
                $attrs['class']=" select2-input";
            }
            foreach($attrs as $a=>$v){
                 $str .="$a=\"$v\"";
            }
            $value = $this->getValue();
            // if(is_array($value)){
            //     $value = implode(",",$value);
            // }
            $select  = "<select $str name=\"$name\"  >";
            $select.="<option   value=''>Select</option>";
            foreach($options as $key=>$option){

                $stro="";
                if(isset($optionattr[$key])){
                        foreach($optionattr[$key] as $atr_name=>$val){
                            $stro .= "$atr_name='$val'";
                        }

                }
               
                if(is_array($value))
                    $selected =  in_array($key,$value) ?"selected":"";
                else{
                        $selected =  $key==$value ?"selected":"";
                }
                $select.="<option $selected $stro value='$key'>$option</option>";
            }

            $select.='</select>';
            // $optionattr
            $input=$select;//\Form::select($name,$options,$this->getValue(), $attrs);
        }
        else if($type=="radio"){
            $radio ="";  
            foreach($options as $k=>$option)
            $radio  .=  \Form::radio($name,$k,$k==$this->getValue() ?true:false,$attrs) . " $option";
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
