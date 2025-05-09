<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class ChangeStatusComponent extends ViewComponent{
    

    function js(){
        return "
        <script>
                function onChangeStatusSuccess(event){
                    if($(event).hasClass('active')){
                        $(event).removeClass('active'); 
                        $(event).addClass('inactive');
                        $(event).html('In Active');
                    }
                    else{
                        $(event).removeClass('inactive'); 
                        $(event).addClass('active');
                        $(event).html('Active');
                    }

                    
                }
                
                function changeStatusComponent(e,that) {
                 e.preventDefault();
                  
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
                             swal.close()
                            //  runCrudAjax(e,that)
                             window.location.href = $(that).attr('href');
            
                        });
                        return false;
                }
        </script>
        ";
    }
    function view(){
        $name = $this->config["name"];
        $url = $this->getConfig("url","");

        
              

            return new LinkComponent(["name"=>"change_status",
            "attr"=>["class"=>"list-status ".($this->getValue()=="1"?"active":"inactive"),"onclick"=>"changeStatusComponent(event,this);"],
            "href"=>$url,
            "label"=>($this->getValue()=="1"?"Active":"In Active"),
            
            
            "onsuccess"=>"onChangeStatusSuccess(event)"]);// "<a title='Manage Status' class='list-status active ' onclick='return changeStatusComponent(this)' href='".$url."'>Active</a>";
        
         
        
      
    }

}