<?php
namespace Aman5537jains\AbnCmsCRUD\Lib;

use Aman5537jains\AbnCmsCRUD\Components\InputComponent;
use Aman5537jains\AbnCmsCRUD\Components\SelectComponent;

class Form{

    public static function createAttributes($params){
        $attrs='';
        foreach($params as $key=>$value ){
            if($key=="required"){
                if($value){
                    $attrs.=" $key";
                }
            }
            else
            $attrs.=" $key=$value ";
        }
        return  $attrs;
    }
    public static function __callStatic($name,$params){

        if($name=="text" || $name=="number" || $name=="email"  || $name=="color"  || $name=="datetimeLocal" ){
            $attrs = self::createAttributes($params[2]) ;
            return "<input type='$name' value='{$params[1]}' name='{$params[0]}' $attrs  />";
        }

        if($name=="textarea"    ){
            $attrs = self::createAttributes($params[2]) ;
            return "<textarea    name='{$params[0]}' $attrs  >$params[1]</textarea>";
        }

        else if ($name=="password" || $name=="file"){
            $attrs = self::createAttributes($params[1]) ;
            return "<input type='$name'   name='{$params[0]}'  $attrs />";
        }
        else if ($name=="select"){
            return  new  InputComponent(
                array_merge(
                ["name"=>$params[0],"options"=>$params[1],
                "value"=>$params[2],"type"=>"select",

                "options-attr"=>@$params[4]],$params[3])
            );
        }
        return $name;
    }
}
