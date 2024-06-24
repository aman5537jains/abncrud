<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class SubmitButtonComponent extends ViewComponent{


    function init(){
        $this->setConfig("col","col-12");
    }
    function view(){

        $url =  $this->getConfig("url","");


      return  '<div class="dForm-actions form-buttons">
                <a class="buttons grey" href="'.$url.'"  name="button">Cancel</a>
                <button class="buttons secondary" type="submit">'.$this->getConfig("label","Submit").'</button>

            </div>
            <div class="dForm-actions loader" style="display: none;">
                <a class="buttons grey" href="'.$url.'"  style="float: left">Cancel</a>
                <a class="buttons secondary" href="javascript:;"><img src="'.url('public/asset/images/loader-white.gif').'" class="loader_img"> </a>

            </div>';


    }

}
