<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;

class SelectAjaxComponent extends InputComponent{
    

    function init(){
        parent::init();
       
        $this->addAttributes([
            "data-url"=> $this->getConfig("url",""),
            "data-placeholder"=> $this->getConfig("placeholder","Select"),
            "data-params"=> $this->getConfig("params",""),
            
        ]);
        $this->setConfig("type","select");

    }
    function setValue($value){
      
        if(!empty($value)){
             
            $this->addAttributes([
                "data-value"=> is_array($value)?implode(",",$value):$value,
             ]);
        }
        return parent::setValue($value);

    }
    function registerJsComponent(){
        if($this->getConfig("type")=="select"){
            return "function(component,config){
        
                    setTimeout(()=>{
                         let select  =  $(component).find('select');
                           select.select2({
                                placeholder: select.data('placeholder'),
                                closeOnSelect: true,
                                delay: 250,
                                cache: true,
                                multiple:select.attr('multiple') ? true : false,
                                ajax: {
                                    url: select.data('url'),
                                    type:'GET',
                                    data: function (params) {
                                        var dynmaicParam  = {}
                                        if(select.data('params')){
                                            let fn = new Function('event',select.data('params'));
                                            dynmaicParam=  fn(select)
                                        }
                                        
                                       
                                        var query = {querystr: params.term,...dynmaicParam}
                                        return query;
                                    },
                                    processResults: function (data, params) {
                                        let all = []
                                        $.each(data,function(key,value){
                                            all.push({id:key,text:value})
                                        })
                                        
                                        return {results:all };
                                    }
                                },
                            });;
                            
                            if(select.data('value')){  
                                     var dynmaicParam  =  {};
                                     if(select.data('params')){
                                        let fn = new Function('event',select.data('params'));
                                        dynmaicParam=  fn(select)
                                     }
                                 
                                 $.ajax({
                                    url: select.data('url'),
                                    type:'GET',
                                    data:  dynmaicParam,
                                    success:function(data){
                                        $.each(data,function(key,value){
                                            const option = new Option(value, key, true, true);
                                            select.append(option).trigger('change');
                                        })
                                        
                                    }
                                 })
                            }
                     },200)
                
            }";
        }
        else{
            return false;
        }
    } 

   
    

}