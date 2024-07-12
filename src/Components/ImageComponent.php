<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class ImageComponent extends ViewComponent{



    function view(){
        $height = $this->getConfig("height",100);
        $width = $this->getConfig("width",100);

        return  "<img src='".url($this->getValue())."' height='$height' width='$width'  />";
    }

}
