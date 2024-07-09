<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class ConfigBuilderComponent extends FormComponent{
    private $formBuilder;


    function jsConfig()
    {

        return ["form"=>$this->getValue()];
    }
    function registerJsComponent()
    {






        return "{
            init(){
                // console.log(this.form,'form')
                this.\$watch('form',(value)=>{
                    this.class_config = JSON.stringify(value);
                })
            },
            form :JSON.parse(config.form),
            config_val:config.form


        }";
    }
    public function init(){
        parent::init();


        $this->formBuilder= new FormBuilder(["name"=>"formbuilder"]);

        $this->formBuilder->setConfig("form",false);
        $fields = $this->getConfig("fields",[]);


        foreach($fields as $name=>$field){

            if(is_array($field) || is_object($field) ){
                $field->setConfig("input-class","dForm-control config_input");
                $field->addAttributes(["x-model"=>"form.".$name]);
                // $field->setConfig("name","form.".$name);
                $this->formBuilder->addField($name,$field);
            }
            else{
                $newfield = new InputComponent(["name"=>$name,"value"=>$field,"input-class"=>"dForm-control config_input"]);
                $newfield->addAttributes(["x-model"=>"form.".$name]);

                $this->formBuilder->addField($name,$newfield);
            }
        }
        // $input = new InputComponent(["name"=>$this->getConfig("name"),"value"=>$this->getValue(),"type"=>"hidden","attr"=>["x-model"=>"config_val"] ]);

        // $this->formBuilder->addField($this->getConfig("name"),$input);



    }


    public function setValue($values)
    {

        parent::setValue($values);
        $this->formBuilder->setValue(json_decode($values,true));

        return $this;
    }


    function view(){


            return  $this->formBuilder->render();



    }
}

