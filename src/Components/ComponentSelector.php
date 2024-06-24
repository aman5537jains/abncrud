<?php

namespace Aman5537jains\AbnCmsCRUD\Components;
use Intervention\Image\Facades\Image;
use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;
use Aman5537jains\AbnCmsCRUD\Layouts\MultiFormBuilder;
use Illuminate\Support\Facades\Blade;


class ComponentSelector extends FormBuilder{

    public $configBuilder;
    public $attrOptions=[];
    function jsConfig()
    {


        return ["url"=>$this->getConfig("url",route("component-render"))  ];
    }
    function registerJsComponent(){



        return " {
            config:{},
            class_name:'1',
            class_config:'1',
            modelContent:'',
            isOpen:'',
            id:'selectcmp',

            onMount(){

            },

            init(){

                this.config=config;

                this.\$nextTick(() =>{
                    this.class_name = this.\$refs.selectcmp.selectedOptions[0].getAttribute('data-class')
                    this.class_config =this.\$refs.selectcmp.selectedOptions[0].getAttribute('data-config')
                })


            },
            onChange(event){

                this.class_name = event.target.selectedOptions[0].getAttribute('data-class')
                this.class_config =event.target.selectedOptions[0].getAttribute('data-config')
            }
         }";
    }



    function init(){
        parent::init();
        $this->setConfig("form",false);
        if(!$this->getConfig("config_field_name",false)){
            $this->setConfig("config_field_name","config");
        }

        $this->configBuilder = new ConfigBuilderPopupComponent(["name"=>$this->getConfig("config_field_name","config")]);

        $options = $this->getConfig("options",[]);
        $selectOptions = [];
        $this->attrOptions = [];

        foreach($options as $k=>$v){
             $selectOptions[$k]=$k;
             $class =$v['class'];
             $config=isset($v['config'])? (json_encode($v['config'])):"[]";

             $this->attrOptions[$k]=["data-class"=>$class, "data-config"=>$config];
        }

        $this->addField($this->getConfig("name"),(new InputComponent([
                                                "name"=>$this->getConfig("name"),
                                                "type"=>"select",
                                                "options"=>$selectOptions,
                                                "options-attr"=>$this->attrOptions,
                                                "attr"=>["@change"=>"onChange(\$event)","x-ref"=>"selectcmp"]])
                                                )->addClass("component-selector"));
        $this->addField($this->getConfig("config_field_name","config"),$this->configBuilder);
    }

    function setValue($val){

        if($val==""){

            return $this;
        }

        if($this->hasField($this->getConfig("name"))){
            $this->getField($this->getConfig("name"))->setValue($val);

        }
        if($this->hasField($this->getConfig("config_field_name"))){
            $data = $this->getData();

            if(isset($data[$this->getConfig("config_field_name")])){

                $this->getField($this->getConfig("config_field_name"))->setValue($data[$this->getConfig("config_field_name")]);
                // $this->configBuilder->addAttributes(["data-class"=>$this->attrOptions[$val]["data-class"],"data-config"=>$data[$this->getConfig("config_field_name")]]);
                $field = $this->getField($this->getConfig("name"));
                $attrs = $field->getConfig("options-attr");
                $attrs[$val]["data-config"]=$data[$this->getConfig("config_field_name")];
                $field->setConfig("options-attr",$attrs);
            }

        }
        return $this;
    }
    function onSaveModel($model){

        $model = $this->getField($this->getConfig("name"))->onSaveModel($model);
        $model = $this->getField($this->getConfig("config_field_name","config"))->onSaveModel($model);

        return $model;
    }


}


class ConfigBuilderPopupComponent extends FormComponent{

    private $PopupComponent;


    function jsConfig()
    {
        return ["url"=>$this->getConfig("url",route("component-render")),"class_config"=>function(){
            return "function(){return getConfig()}";
        }];
    }
    function registerJsComponent()
    {





        return "{



            isOpen : false,
            modelContent:'',

            init(){

                // this.\$watch('class_name',(value) => {
                //     console.log('class chahngeddd',this.class_name,this.class_config,config);
                // });
                // this.\$watch('class_config',(value) => {
                //     console.log('class chahngeddd',this.class_name,this.class_config,config);
                // });
            },

            setContent(content){
                this.modelContent = content;
            },
            setModel(status){
                this.isOpen = status;
                if(this.isOpen){
                    console.log(this.class_name,this.class_config,'elem2')
                    this.buildComponent(this.class_name,this.class_config)
                }

            },





            buildComponent(cls,cnf){

                let name = this.\$el.parentNode.parentNode.getAttribute('name');
                if(!cls){
                    this.setContent('<div>Please select drop down value first</div>');
                    return 1;
                }
                this.setContent('<div>Building your component...</div>');
                $.get(this.config.url+'?class='+cls+'&config='+cnf+'&name='+name, (data)=>{
                    // console.log('alpines',window['crudBuilderJS'].alpines);
                    for(let js in data.js){
                        if(!window['crudBuilderJS'].alpines[js])
                        {
                            $('body').append(data.js[js]);
                        }
                        // if(!crudBuilderJS.isRegistered(js)){
                        //      $('body').append(data.js[js]);
                        // }
                    }
                     this.setContent(data.html);

                })
            }
        }";
    }
    public function init(){
        parent::init();
        $this->PopupComponent =  new PopupComponent(["label"=>"Config","content"=>"","onOpen"=>"onOpen(component)"]);

    }

    function view(){

            $name = $this->getAttributesString();

           return  "<div class='config-builder' :class_name='class_name'  >
                 <input type='hidden'  x-model='class_config' $name />
            ". $this->PopupComponent."</div>";

    }
}
