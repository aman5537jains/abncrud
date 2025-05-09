<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts; 
class LiveFormBuilder  extends FormBuilder
{   
    function registerJsComponent(){
        $calledComponnet = get_called_class();

        return "function(component,config){

        }";
    }
     function js(){

        return '<script>
                function rerender(){
                       $.ajax({
                                url:  "live-update",
                                type: "POST",
                                data: formData,
                                cache: false,
                                processData: false,
                                contentType: false,
                                    headers: {
                                    "Accept": "application/json"
                                },
                                success: function (data) {
                                    if(data.status){
                                            
                                    }
                                    else{
                                        
                                        
                                    }
                                },
                                 error:function(xhr){
                                        if (xhr.status === 422) {
                                        $("label.error").remove();
                                         }
                                        else{
                                            alert("ERROR");
                                        }
                                        

                                 }})
                }
        <script>';
     }
}
