<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class RelationalTextComponent extends TextComponent{



    function getValue()
    {
        $relation  = $this->getConfig("relation",null);
        $column  = $this->getConfig("column",null);
        $row  = $this->getData()["row"];

        if(is_array($row->{$relation}) || $row->{$relation} instanceof \Illuminate\Database\Eloquent\Collection){
            $names=[];

            foreach($row->{$relation} as $value){
                $names[]= $value->{$column};
            }
        }
        else{
            $names[]=$row->{$relation}->{$column};
        }
        return   implode(", ",$names);
    }



}
