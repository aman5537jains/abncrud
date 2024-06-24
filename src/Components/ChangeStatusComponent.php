<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class ChangeStatusComponent extends ViewComponent{


    function js(){
        return "
        <script>

                function changeStatusComponent(obj) {
                    swal({
                            title: 'Are you sure?',
                            text: '',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonClass: 'btn-danger',
                            confirmButtonText: 'Yes',
                            closeOnConfirm: false
                        },
                        function() {
                            window.location.href = $(obj).attr('href');

                        });
                        return false;
                }
        </script>
        ";
    }
    function view(){
        $name = $this->config["name"];
        $url = $this->getConfig("url","");

        // $data = $this->getData();
        // $module =@$this->controller->controller->module;
        // $uniqueKey =@$this->controller->controller->uniqueKey;
        if($this->getValue()=='1'){
            return "<a onclick='return changeStatusComponent(this)' href='".$url."'>Active</a>";
        }
        else{
            return "<a onclick='return changeStatusComponent(this)' href='".$url."'>In Active</a>";

        }



    }

}
