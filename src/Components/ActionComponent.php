<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class ActionComponent extends ViewComponent{
    

    function js(){
        return "
        <script>
                 
                function confirmDeleteComponent(obj) {
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
        $row= $this->getData()['row'];
        $url =$this->getConfig("url","");
        $module =$this->getConfig("module","");
        $id = $this->getConfig("uniqueKey","id"); ;
        $edit_button = $this->getConfig("edit_button",true); 
        $delete_button = $this->getConfig("delete_button",true); 
        $view_button = $this->getConfig("view_button",true); 
        
        return $this->loadView("action",compact("row","url","id",'module','edit_button','delete_button','view_button'));
    }

}