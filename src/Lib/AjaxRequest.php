<?php 

namespace Aman5537jains\AbnCmsCRUD\Lib;
 
class AjaxRequest  
{

    function __construct($url,$payload=[]){
        $this->url = $url;
        $this->payload = $payload;
    }
    function js($js){
       
        $this->js = "
        var config = {};
        $js
        runAjax();";;
    }

    function run(){

    }



}