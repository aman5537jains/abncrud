<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts;

use Aman5537jains\AbnCmsCRUD\Components\InputComponent;
use Aman5537jains\AbnCmsCRUD\Components\FileInputComponent;
use Aman5537jains\AbnCmsCRUD\Components\SubmitButtonComponent;
use Illuminate\Support\Facades\Validator;

class FormBuilder  extends OneRowLayout
{
    public $template;
    public $values =[];
    public $formErrors =[];


    public function init(){
        parent::init();
        $this->setConfig("input-layout","AbnCmsCrud::crud.form-input");

    }
    // function registerJsComponent(){
    //     return "(component,config){

    //          initFormBuilder(component,config);

    //     }";
    // }

    function jsConfig(){
        return  ["ajax"=>$this->getConfig("ajax",false),"back_url"=>$this->getConfig("back_url",""),
        "onSuccess"=>function(){
           return $this->getConfig("onSuccess","function(){
            console.log(aaa);
           }");
        }];
    }

    public function formFields($builder){
        $arr=[];
        $model =    $this->getModel();
        $columns=  \DB::select("SHOW COLUMNS FROM ". (new $model)->getTable());
        $formcolumns = (config("crud.form_fields"));

        foreach($columns as $column){
            if(
                $column->Extra!="auto_increment"
                && $column->Field!="updated_at"
                && $column->Field!="slug"
                && $column->Field!="created_at"
            ){
                $class = InputComponent::class;
                $option=["validations"=>["required"]];
                if(substr( $column->Type, 0, 3 ) === "int")
                {
                    $class = InputComponent::class;
                    $option = ["type"=>"number","validations"=>["required"]];
                }
                else if(substr( $column->Type, 0, 7 ) === "varchar")
                {
                    if( strpos($column->Field, "file") !== false){
                        $class = FileInputComponent::class;
                        $option = ["validations"=>["required"]];
                    }
                    else
                    $class = InputComponent::class;

                }
                else if(substr( $column->Type, 0, 4 ) === "enum")
                {
                    $class = InputComponent::class;
                    preg_match("/^enum\(\'(.*)\'\)$/", $column->Type, $matches);
                    $enum = explode("','", $matches[1]);


                    $option = ["type"=>"select","options"=>array_combine($enum, $enum),"validations"=>["required"]];
                }
                else if($column->Type === "date")
                {
                    $class = InputComponent::class;
                    $option = ["type"=>"date","validations"=>["required"]];
                }
                else if(substr( $column->Type, 0, 4 ) === "datetime")
                {
                    $class = InputComponent::class;
                    $option = ["type"=>"datetime","validations"=>["required"]];
                }
                else if(substr( $column->Type, 0, 4 ) === "text")
                {
                    $class = InputComponent::class;
                    $option = ["type"=>"textarea","validations"=>["required"]];
                }

                $option["validations"]=$column->Null=="NO"?["required"]:[];
                $arr[$column->Field]=["class"=>$class,"config"=>$option];


                if(isset($formcolumns[$column->Field])){
                    $arr[$column->Field]=$formcolumns[$column->Field];
                }
            }
        }
        $arr['submit']=["class"=>SubmitButtonComponent::class,"config"=>["label"=>"Save","url"=>$this->getConfig("back_url","")]];
        $model = $this->getModel();

        foreach($arr as $fldName=>$opt){
            $builder->addField($fldName,$opt);
        }
        method_exists($model,'crudFormColumns')? $model::crudFormColumns($builder):[];
        return $arr;
   }
    function validate(){
        $this->build();

        return Validator::make($this->getValue(),$this->validations());
    }
    function reset(){
        foreach($this->getFields() as $k=>$value){
            $value->setData([]);
            $value->setValue('');
        }
    }
    function setValue($values){
        foreach($this->getFields() as $k=>$value){
            $value->setData($values);
             if(isset($values[$k])){
                $value->setValue($values[$k]);
             }
        }
        // foreach($values as $k=>$value){

        //     if($this->hasField($k)){

        //          $this->getField($k)->setData($values)->setValue($value);

        //     }else{

        //     }
        // }

        return $this;
    }

    function getValue(){
        $arr=[];
        foreach($this->getFields() as $k=>$value){
            $arr[$k]= $this->getField($k)->getValue($value);
        }
        return $arr;
    }

    function validations()
    {
        $allRule=[];
        foreach($this->getFields() as $field=>$value){
            if($value instanceof \Aman5537jains\AbnCmsCRUD\FormComponent || $value instanceof \Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder) {
                if($value instanceof \Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder){
                    $allRule+=  $value->validations();
                }else{
                    $allRule[$value->getConfig("name")] =  $value->validations();
                }
            }
        }

         return $allRule;
    }

    function setModel($model){
        parent::setModel($model);
        if($this->getConfig("autoBuild",false))
            $this->formFields($this);
    }
    function addError($name,$message){
        $this->formErrors[$name]= $message;
        return $this;

    }
    function validateAndSave($inputs){

            $this->setValue($inputs);

            $validator = $this->validate();

            if(count($this->formErrors)>0)
            {  $validator->after(function ($validator) {
                    foreach($this->formErrors as $name=>$message){

                        $validator->errors()->add($name, $message);

                    }
                });
            }

            if ($validator->fails())
            {
                if($this->getConfig("ajax",false)){
                    return response()
                    ->json(["status"=>false,"message"=>"There are some errors in the form","errors"=>$validator->errors()],400);

                }
                else{
                    return (object)["status"=>false,"data"=>$validator->errors(),"message"=> "There are some errors in the form"];
                }
            }
            else
            {

                if($this->getConfig("ajax",false)){

                    return response()->json(["status"=>true,"data"=>$this->save(),"message"=>"Record Saved Successfully"]);

                }
                else{
                    return  (object)["status"=>true,"data"=>$this->save(),"message"=> "Record Saved Successfully"];
                }


            }


    }

    function save(){
        $this->build();

        if(!empty($this->getModel())){
            \DB::beginTransaction();
            try{

                $model= $this->onSaveModel($this->getModel());
                $model = $this->beforeSave($this,$model);

                $model->save();
                $realtions = $model->getRelations();

                foreach($realtions as $key=>$relation){

                    $model->{$key}()->saveMany($relation);
                }
                \DB::commit();
                $this->afterSave($this,$model);
                return $model;
            }
            catch(\Exception $e){
                \DB::rollback();
                throw $e;
            }
        }else{
            throw new \Exception("Model not set for save the values");
        }
    }
    public function beforeSave($form,$model){
        $fn = $this->getConfig("beforeSave",function($form,$model){
            return $model;
        });
        return $fn($form,$model);
    }
    public function afterSave($form,$model){
        $fn = $this->getConfig("afterSave",function($form,$model){
            return $model;
        });
        return $fn($form,$model);

    }
    function onSaveModel($model){

        foreach($this->getFields() as $key=>$value){

            if($value instanceof \Aman5537jains\AbnCmsCRUD\FormComponent || $value instanceof \Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder) {
                $model = $value->onSaveModel($model);

            }
        }

        return $model;
    }
    function defaultComponent(){
            return InputComponent::class;
    }


    function js(){

        return <<<script
                 <script>
                 function initFormBuilder(component,config){
                    console.log("formmmmmmmm",component,config);
                    let that = $(component).find(".form-builder-forms form")[0];
                    let formParent = $(component);

                    if(!config.ajax){
                        return false;
                    }
                    $(that).on("submit",function(e){
                        e.preventDefault();

                        let form =$(this);
                        console.log('form',form);
                        if( $(that).valid()){

                            var formData = new FormData(that);
                            console.log('formData',formData);
                            let action = $(that).attr("action");
                            let method = $(that).attr("method");
                            $.ajax({
                                url:  action,
                                type: method,
                                data: formData,
                                cache: false,
                            processData: false,
                            contentType: false,
                                success: function (data) {
                                    formParent.find(".form-builder-err").remove();
                                    // form.trigger("reset");
                                    form.find(".loader").hide();
                                    form.find(".form-buttons").show();
                                    // if(config.back_url){
                                    //     window.location = config.back_url;
                                    // }
                                    // else{
                                    //     alert(data.message);
                                    //

                                    // }
                                    if(config.onSuccess){
                                        const fnString = config.onSuccess;
                                        const fn = eval(fnString);
                                        fn(config,data);
                                        //  let successFn = new Function("config","data",config.onSuccess);
                                        //  successFn(config,data);
                                    }
                                    form.after("<span class='success form-builder-err'> "+data.message+"</span>");

                                },
                                error:function(data){
                                    console.log('form',form);
                                    formParent.find(".form-builder-err").remove();
                                    form.find(".loader").hide();
                                    form.find(".form-buttons").show();

                                    form.after("<span class='error form-builder-err'> "+data.responseJSON.message+"</span>");
                                    for(name in data.responseJSON.errors){
                                        if(form.find("[name="+name+"]").length<=0){
                                            form.before("<span class='error form-builder-err'> "+data.responseJSON.errors[name]+"</span>");
                                        }
                                        else{
                                            form.find("[name="+name+"]").after("<span class='error form-builder-err'> "+data.responseJSON.errors[name]+"</span>");
                                        }
                                    }
                                    if(config.onError){
                                        config.onError(data);
                                    }

                                },
                                cache: false,
                                contentType: false,
                                processData: false
                            });
                        }
                    })
                }

                </script>
        script;
    }
    function beforeRender($cmp){


    }

    function view(){
        $uid=  $this->componentID();


        return  '<div data-cid="'.$uid.'"  class ="form-builder-forms '.$uid.'" >'.view($this->getConfig("form-layout","AbnCmsCrud::crud.form-component"),
        ["fields"=>$this->inputLayout(), "component"=>$this]
        )->render() . '</div>';
    }

}
