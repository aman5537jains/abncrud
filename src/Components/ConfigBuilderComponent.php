<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class ConfigBuilderComponent extends FormComponent{
    private $formBuilder;
    public function init(){
        parent::init();

        //  = $this->setConfig("name","form");
        $this->formBuilder= new FormBuilder(["name"=>"formbuilder"]);

        $this->formBuilder->setConfig("form",false);
        $fields = $this->getConfig("fields",[]);


        foreach($fields as $name=>$field){

            if(is_array($field) || is_object($field) ){
                $field->setConfig("input-class","dForm-control config_input");
                $this->formBuilder->addField($name,$field);
            }
            else{
                $this->formBuilder->addField($name,new InputComponent(["name"=>$name,"value"=>$field,"input-class"=>"dForm-control config_input"]));
            }
        }

        $html =new HtmlComponent(["name"=>"submit"]);
        $html->setView("<button type='button' class='saveconfig'>Save</button>");
        $html->setJs("<script>

            $(document).on('click','.saveconfig',function(){
                let json = {};
                $('.config_input').each(function(k,v){
                    json[$(this).attr('name')]=$(this).val();

                    console.log($(this).val(),$(this).attr('name'));
                })

                $('input[name=config]').val(JSON.stringify(json));
            });
        </script>");
        $this->formBuilder->addField("submit",$html);
    }


    public function setValue($values)
    {

        parent::setValue($values);
        $this->formBuilder->setValue(json_decode($values,true));

        return $this;
    }


    function view(){
        $form =$this->formBuilder->render();
        $input = new InputComponent(["name"=>$this->getConfig("name"),"value"=>$this->getValue(),"type"=>"hidden"]);

        $PopupComponent =  new PopupComponent(["label"=>"Config","content"=>$form.$input]);

       return  "<div class='config-builder' >".$PopupComponent."</div>";
    }
}
