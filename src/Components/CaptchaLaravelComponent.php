<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class CaptchaLaravelComponent extends FormComponent{


    function validations()
    {
        return [
            "required","captcha"
        ];
    }
    function js(){
        return "";
    }
    function view(){
        $form='';
        $form .= '<p>' . captcha_img() . '</p>';
        $form .= '<p><input type="text" name="captcha"></p>';
        return $form;
    }

}
