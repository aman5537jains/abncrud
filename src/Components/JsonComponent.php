<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

 
use Aman5537jains\AbnCmsCRUD\ViewComponent;
use Exception;

class JsonComponent extends ViewComponent{

    
    function js(){
        
        return "";
    }
    function getValue()
    {
        $parent=parent::getValue();
        $formatted  = $this->getConfig("formatted",null);
        
        if($formatted!=null){
           
           return $formatted($parent,$this->getData()["row"]);
        }
        return $parent;
    }
    function parseJson($json){
        $text='';
        foreach($json as $key=>$value){
            if($key=="id")
            continue;
            if(is_string($value)){
                $text .= "$key : $value <br>";
            }else{
                $text .= $this->parseJson($value);
            }
          
        }
        return $text ;
    }
    function view(){
        try{
            $json  = json_decode($this->getValue());
            // dd( $json);
            
            
            return  $this->parseJson($json);
        }
        catch(Exception $e){
            return $this->getValue();
        }
    }

}
