<?php 

namespace Aman5537jains\AbnCmsCRUD\Traits;
 
trait AjaxAttributes  
{
    
    function ajaxAttrSetup(){
        if($this->getConfig("ajax",false))
        {
            $this->addAttributes([
                "data-href"     => $this->getConfig("href",""),
                "data-method"     => $this->getConfig("method","GET"),
                "data-onsuccess"=> $this->getConfig("onsuccess",""),
                "data-onerror"  => $this->getConfig("onerror",""),
                "data-payload"  => $this->getConfig("payload","return {}"),
                "data-beforesend"  => $this->getConfig("beforesend","return {}"),
                $this->getConfig("ajaxEvent","onchange")  => "runCrudAjax(event,this)",
             ]);
        }
    }
}