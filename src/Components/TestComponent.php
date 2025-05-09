<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

 
use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class TestComponent extends FormComponent{

    function registerJsComponent(){
        return "
               function(component,config){
                console.log(component,'evvvv')
                alert(1);
                }
        ";
    }
    function js(){
        
        return "<script>
            function execute(){
               
            }
        </script>";
    }
     
    function view(){
        return "<input type='text' />";
    }

}
