<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Lib\Form;

class SelectComponent extends FormComponent{


    function registerJsComponent(){

        return " {
            config:config,
            init(){
                    console.log('select2',this.\$el,this.config);
                     initSelect2( this.\$el,this.config)
            }
        } ";
    }


    function js(){


                return "
                <link href='https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' rel='stylesheet' />
                <script src='https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'></script>
                <script>

                function initSelect2(component,config){


                    let that = $(component).find('.searchbleselect');
                    console.log('that',that)
                    $(that).select2();
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

                                        if(values.find((vals)=> vals+'' == key+'') > -1){
                                            var option = $('<option selected >'+data[key]+'</option>').val(key);
                                            $(that).append(option)
                                        }
                                        else{
                                         var option = $('<option >'+data[key]+'</option>').val(key);
                                          $(that).append(option)
                                        }
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
        $optionattr  = $this->getConfig("options-attr",[]);
            $values = [$this->getValue()];
            if($this->getConfig("multiple",false)){
                unset($attrs["placeholder"]);

                $attrs  =  $attrs + ["data-placeholder"=>$placeholder];

                $values = $this->getValue();

            }
            $str='';

            foreach($attrs as $a=>$v){
                if(!is_array($v))
                 $str .="$a=\"$v\"";
            }

            $select  = "<select $str name=\"$name\"    >";
            $select.="<option   value=''>Select</option>";

            foreach($options as $key=>$option){

                $stro="";
                if(isset($optionattr[$key])){
                        foreach($optionattr[$key] as $atr_name=>$val){
                            $stro .= "$atr_name='$val'";
                        }

                }
                if(!empty($values))
                    $selected = in_array($key,$values)?"selected":"";
                else
                    $selected ='';

                $select.="<option $selected $stro value='$key'>$option</option>";
            }

            $select.='</select>';
            // $optionattr
            $input=$select;

        return $select;// Form::select($name,$options,$this->getValue(), $attrs,$optionattr);

    }



}
