<?php
namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;
use Aman5537jains\AbnCmsCRUD\Layouts\MultiFormBuilder;
class AddMoreComponent extends MultiFormBuilder{

    public $originalItems = [];
    public $allItems = [];

    function registerJsComponent(){

        return " {
                config:config,

                init(){
                    this.\$watch('config.items',(value)=>{
                        this.config.setter(value);
                    })
                },
                add(){
                    this.config.items.push(JSON.parse(JSON.stringify(this.config.itemOriginal)));
                },
                remove(index){
                    this.config.items.splice(index,1)
                }

        }";
    }

    function jsConfig(){

        $items = $this->getValue();
        if(!$items){
          $items = [];
        }

        $xModel = $this->hasAttribute("x-model");

        $setter="function(value){ }";
        if($xModel){
            $xModel = $this->getAttribute("x-model");
            $setter="function(value){ $xModel = value } ";
        }
        return ["itemOriginal"=>$this->originalItems,"items"=>$items,"key"=>$this->getConfig("name","form"),"setter"=>function()use($setter){
            return $setter;
        }];
    }


    function beforeRender($cmp)
    {
        $fields = $this->getFields();
        $formName = $this->getConfig("name","form");

        $i=0;

        foreach($fields as $key=>$field){
            if($field instanceof FormBuilder){
                foreach($field->getFields() as $innerFields){
                    $attr               = $innerFields->getConfig("attr",[]);
                    $name               = $innerFields->getConfig("name");


                        $attr["x-model"]    = "$formName.$name";
                        $attr[":name"]      = "'".$formName."['+index+'][$name]'";

                    $attr["vname"]      = "$formName.$name";
                    $innerFields->setConfig("attr",$attr);
                    $this->originalItems[$name]= $innerFields->getValue();
                    $i++;
                }

            }
            else{
                $attr               = $field->getConfig("attr",[]);

                $name               = $field->getConfig("name");

                    $attr["x-model"]    = "$formName.$name";
                    $attr[":name"]      = "'".$formName."['+index+'][$name]'";


                $attr["vname"]      = "$formName.$name";
                $field->setConfig("attr",$attr);
                $this->originalItems[$name]= $field->getValue();
                $i++;
            }

        }

        ;

    }
    function init()
    {
        parent::init();
        $this->setConfig("form",false);
    }
    function view(){
        $view = parent::view();
        $key = $this->getConfig("name","form");
        $label = $this->getLabel();
        return "<div class='addMoreapp' >$label  <template x-for='($key,index) of config.items'>  <div> ".$view." <a href='javascript:void(0)' @click='remove(index)'>X</a></div></template> <button class='buttons secondary' type='button' @click='add()'>Add More</button></div>";
    }
}
