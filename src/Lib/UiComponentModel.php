<?php 

namespace Aman5537jains\AbnCmsCRUD\Lib;

use Aman5537jains\AbnCmsCRUD\Components\InputComponent;
use Aman5537jains\AbnCmsCRUD\Components\LinkComponent;
use Aman5537jains\AbnCmsCRUD\Components\TextComponent;

trait UiComponentModel  
{

    function uiComponents(){
      
        return [

            
                // "title"=>[
                //     "form"=>   (new InputComponent([],null)),
                //     "view"=>   (new LinkComponent(["label"=>"View ".$title,"link"=>url("taxes/".$this->slug."/edit")],null))
                // ]
             
        ];
    }
    function getUiAttribute(){
       return $this->uiComponents();
    }

    function component($name,$type="view"){

        $items = $this->uiComponents();
        if(isset($items[$name]) && isset($items[$name][$type])){
            $component =  $items[$name][$type];
        }
        else{
            if($type=="view")
                $component =  (new TextComponent(["name"=>$name],null));
            else
                $component =  (new InputComponent(["name"=>$name],null));
        }

        $attributes = $this->getAttributes();

        if(isset($attributes[$name])){
            $component->setValue($attributes[$name]);
        }
        return $component;
 
     }
    
     

}
 