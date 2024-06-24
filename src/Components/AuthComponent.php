<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Illuminate\Support\Facades\Blade;



class AuthComponent extends FormComponent{

    function isVisible(){
        return false;
 }

    function getValue(){

        if($this->getConfig("branch_id",false)){
            return  getMyCurrentBranch();
        }
        $loginUserData = getFrontLoginUser();
        $staff_id=0;
        if($loginUserData['type']=="BUSINESS")
        {
            $business_id =$loginUserData['id'];
        }else{
            $business_id =$loginUserData['business_id'];
            $staff_id = $loginUserData['id'];
        }
        if($this->getConfig("business_id",false)){
            return  $business_id;
        }
        if($this->getConfig("user_id",false)){
            return  $business_id;
        }
        if($this->getConfig("staff_id",false)){
            return  $staff_id;
        }
        return 0;

    }

    function view(){
        return "";
    }



}
