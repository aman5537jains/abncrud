<?php

namespace Aman5537jains\AbnCmsCRUD\Components;
use Intervention\Image\Facades\Image;
use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;
use Aman5537jains\AbnCmsCRUD\Layouts\MultiFormBuilder;
use Illuminate\Support\Facades\Blade;


class ComponentSelector extends FormBuilder{


    function jsConfig()
    {
        return ["url"=>$this->getConfig("url",route("component-render"))];
    }
    function registerJsComponent(){
        $fromnam = $this->getConfig("name","view_name");
        $name = $this->getConfig("config_field_name","config");
         $value = $this->getField($this->getConfig("name","view_name"))->getValue();

        return "(component,config)=>{

            let select =  $(component).find('select')[0];
            let configBuilder =  $(component).find('.config-builder')[0];
            if('$value'!=''){
                buildComponent();
            }
            $(select).on('change',function(){
                buildComponent();

            })

            function buildComponent(){
                $.get(config.url+'?name=$name',function(data){

                    for(let js in data.js){
                        if(!crudBuilderJS.isRegistered(js)){
                            $('body').append(data.js[js]);
                        }
                    }

                    $(configBuilder).html(data.html);
                })
            }
        }";
    }



    function init(){
        parent::init();

        if(!$this->getConfig("config_field_name",false)){
            $this->setConfig("config_field_name","config");
        }

        $builder = new ConfigBuilderComponent(["name"=>$this->getConfig("config_field_name","config")]);

        $this->addField($this->getConfig("name"),new InputComponent(["name"=>$this->getConfig("name"),"type"=>"select","options"=>$this->getConfig("options",[])]));
        $this->addField($this->getConfig("config_field_name","config"),$builder);
    }

    function setValue($val){
        if($this->hasField($this->getConfig("name")))
            $this->getField($this->getConfig("name"))->setValue($val);

        if($this->hasField($this->getConfig("config_field_name"))){
            $data = $this->getData();

            if(isset($data[$this->getConfig("config_field_name")])){
                $this->getField($this->getConfig("config_field_name"))->setValue($data[$this->getConfig("config_field_name")]);
            }

        }

    }
    function onSaveModel($model){

        $model = $this->getField($this->getConfig("name"))->onSaveModel($model);
        $model = $this->getField($this->getConfig("config_field_name","config"))->onSaveModel($model);

        return $model;
    }


}
