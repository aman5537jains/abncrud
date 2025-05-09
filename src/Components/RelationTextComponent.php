<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

 
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class RelationTextComponent extends ViewComponent{
    
    function view(){
        //  $relation  = $this->getConfig("relation");

        $data = $this->getData();
        // $data["row"]->{$relation}
        return $this->getValue();
    }

}