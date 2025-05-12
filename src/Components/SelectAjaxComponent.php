<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;

class SelectAjaxComponent extends InputComponent{
    

    function init(){
        parent::init();
        if($this->getConfig("onSearch",false)){
            // $this->setupLiveUpdate();
        }
        $this->addAttributes([
            "data-onsearchurl"=> $this->getConfig("url",""),
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
                         console.log({select:select})
                           select.select2({
                                placeholder: select.data('placeholder'),
                                closeOnSelect: true,
                                delay: 250,
                                cache: true,
                                multiple:select.attr('multiple') ? true : false,
                                ajax: {
                                    url: select.data('onsearchurl'),
                                    type:'GET',
                                    data: function (params) {
                                        var dynmaicParam  = {}
                                        if(select.data('params')){
                                            let fn = new Function('event',select.data('payload'));
                                            dynmaicParam=  fn(select)
                                        }
                                        
                                       
                                        var query = {querystr: params.term,...dynmaicParam}
                                        return query;
                                    },
                                    processResults: function (data, params) {
                                        console.log({data})
                                        let all = []
                                        $.each(data,function(key,value){
                                            all.push({id:key,text:value})
                                        })
                                        
                                        return {results:all };
                                    }
                                },
                            });; 
                            $(select).on('select2:open', function () { alert(1)
  let searchField = document.querySelector('.select2-search__field');

  if (!searchField) return;

  searchField.addEventListener('input', debounce(function (e) {
    let searchTerm = e.target.value;

    // Do your custom AJAX here
    $.ajax({
      url: '/custom-search-endpoint',
      method: 'POST',
      data: { query: searchTerm },
      success: function (data) {
        // Clear existing options
        $('#user-select').empty();

        // Add new options
        data.items.forEach(item => {
          let newOption = new Option(item.name, item.id, false, false);
          $('#user-select').append(newOption);
        });

        // Manually trigger update
        $('#user-select').trigger('change');
      }
    });

  }, 300)); // 300ms debounce
});
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