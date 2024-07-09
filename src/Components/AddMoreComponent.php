<?php
namespace Aman5537jains\AbnCmsCRUD\Components;
use Aman5537jains\AbnCmsCRUD\Layouts\MultiFormBuilder;
class AddMoreComponent extends MultiFormBuilder{

    public $originalItems = [];
    public $allItems = [];

    function registerJsComponent(){
        return "(component,config)=>{

              if(!component.hasAttribute('data-v-app')){
             const POS = {
                data: function() {
                    return {
                        originalItem:config.itemOriginal,
                        items:config.items
                    }
                },
                mounted:function(){

                    // this.add();
                },
                methods: {
                    add:function(){

                        this.items.push(JSON.parse(JSON.stringify(this.originalItem)));

                    },
                    remove:function(index){

                        this.items.splice(index,1)
                    }
                }
            }
            const addMoreapp = Vue.createApp(POS)
            addMoreapp.mount(component);
            }
        }";
    }

    function js(){
        return '<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>';
    }



    function jsConfig(){

        $items = $this->getValue();
        if(!$items){
          $items = [];
        }

        return ["itemOriginal"=>$this->originalItems,"items"=>$items,"key"=>$this->getConfig("name","form")];
    }


    function beforeRender($cmp)
    {
        $fields = $this->getFields();
        $formName = $this->getConfig("name","form");

        $i=0;
        foreach($fields as $key=>$field){

            $attr = $field->getConfig("attr",[]);
            $name = $field->getConfig("name");
            $attr["v-model"]= "$formName.$name";
            $attr[":name"]="'".$formName."['+index+'][$name]'";
            $attr["vname"]="$formName.$name";
            $field->setConfig("attr",$attr);
            $this->originalItems[$name]=$field->getValue();
            $i++;
        }
        ;

    }

    function view(){

        $view = parent::view();

        $key = $this->getConfig("name","form");
        return "<div class='addMoreapp' > <div v-for='($key,index) of items'>   ".$view." <a href='javascript:void(0)' @click='remove(index)'>X</a></div> <button type='button' @click='add()'>Add</button></div>";
    }
}
