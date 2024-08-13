<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class ActionMoreComponent extends ViewComponent{



    function view(){
        $rendered='';
        $componenets = $this->getConfig("components",[]);
        $outer = '';
        if(count($componenets)>2){
            $outer .='<div class="list-actions list-actions-wrap">
                        <div class="list_actions_btn"></div>
                        <div class="list_actions_dropdown list_actions_dropdown_w_170">
                            <div class="list_actions_dropdown_content">
            ';
        }
        foreach($componenets as $componenet){
                $rendered.=$componenet->setValue($this->getValue())
                                        ->setData($this->getData())
                                        ->render();
        }
        if(count($componenets)>2){
            $outer .= " $rendered
                        </div>
                    </div>
                </div>";
        }
        else{
            return $outer.$rendered;
        }



    }

}
