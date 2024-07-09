<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;



class AuthComponent extends FormComponent{

    function isVisible(){
        return false;
 }

    function getValue(){


        if($this->getConfig("user_id",false)){
            return  Auth::user()->id;
        }

        return 0;

    }

    function view(){
        return "";
    }



}
