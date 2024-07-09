<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;

class SelectComponent extends FormComponent{


    function registerJsComponent(){
        return "(component,config)=>{

            initSelect2(component,config)
        }";
    }


    function js(){


                return "

                <script>

                function initSelect2(component,config){


                    let that = $(component).find('.searchbleselect');

                    // var config = JSON.parse($(that).attr('config'));

                    if(config.dependentOf){
                        console.log('config.dependentOf',config.dependentOf)
                        $(config.dependentOf).on('change',function(){
                            $(that).val('').trigger('change');
                        })
                    }


                    if(config.ajax){
                        console.log('select2',$(that).val(),$(that).attr('name'));

                        $(that).select2({
                            placeholder: config.placeholder,
                            closeOnSelect: true,
                            ajax: {
                                url: config.url,
                                headers: {
                                    'X-CSRF-Token': $('meta[name=\"csrf-token\"]').attr('content')
                                },
                                type:'POST',
                                data: function (params) {

                                    var query = {querystr: params.term,field:config.name}
                                    if(config.dependentOf){

                                        query[$(config.dependentOf).attr('name')]=$(config.dependentOf).val()
                                    }

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
                        });
                        if($(that).data('values')!='\"\"'){

                            let values = $(that).data('values');
                            console.log('select2 new - inner',$(that).data('values'),typeof values);
                            if( typeof values =='string' || typeof values =='number'){
                                values = [ values ];
                            }
                            var query = {old_ids:values} ;
                            if(config.dependentOf){
                                query[$(config.dependentOf).attr('name')]=$(config.dependentOf).val()
                            }

                            $.ajax({url: config.url,
                            headers: {
                                'X-CSRF-Token': $('meta[name=\"csrf-token\"]').attr('content')
                            },
                            type:'POST',
                            data: query,
                            success: function (data) {
                                    $(that)
                                    for(key in data){
                                        var option = $('<option selected>'+data[key]+'</option>').val(key);
                                        $(that).append(option)
                                    }
                                    $(that).trigger('change');
                            },
                            error: function (error) {
                                console.log(error)
                            }}
                            )

                        }

                    }
                    else{

                        $(that).select2();



                    }

                }

            </script>
                ";

    }
    function jsConfig()
    {
        return [ "dependentOf"=>$this->getConfig("dependentOf",""),"url"=>$this->getConfig("searchurl",""),"ajax"=>$this->getConfig("ajax",false)];
    }

    function buildInput($name,$attrs){

        $options    = $this->getConfig("options",[]);
        $select2    = $this->getConfig("select2",true);

        $attrs['class'] .=  ($select2?" searchbleselect":"");

        $placeholder = $this->getConfig("placeholder",$this->getConfig("label",""));
        $optionattr  = $this->getConfig("options-attr",[]);
        $attrs['data-values']  =  json_encode($this->getValue()) ;
        if(isset($attrs["multiple"]) && $attrs["multiple"]){
            unset($attrs["placeholder"]);
            $attrs  =  $attrs + ["data-placeholder"=>$placeholder];
        }

        return  \Form::select($name,$options,$this->getValue(), $attrs,$optionattr);

    }



}
