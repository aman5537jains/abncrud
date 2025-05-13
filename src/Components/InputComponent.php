<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Traits\AjaxAttributes;
class InputComponent extends FormComponent{
    
     
    function init(){
        parent::init();
        if($this->getConfig("live",false)){
            $this->enableLiveUpdateSupport("live",$this->getConfig("live",false));
        }
       
    }
    // function setConfig($name, $default = ''){
    //     $config=  parent::setConfig($name,$default);
    //     $this->enableLiveUpdateSupport($name, $default);

    //     return $config;
    // }

    function enableLiveUpdateSupport($name,$default){
        if($name=="live"){
            $fn = $default;
            $fn($this);
           
            $this->setConfig("ajax",true);
            $listiners = htmlspecialchars(json_encode($this->getConfig("listners",[])));
    
            $this->setConfig("payload","let json =$listiners ; return liveUpdateForm([],json,event.form) ");
        
            $this->setConfig("onsuccess","let json =$listiners ;liveUpdate([],json,response.form)");
        }
        if($name=="livesearch" ){
            //  dump("-",$this->getAttributes());
            $this->setConfig("ajaxEvent","onsearch");
         
            $this->setConfig("ajax",true);
            // $this->setConfig("attr",["data-livesearch"=>"true"]);
           
            $inpname=$this->getAttribute("name");
            
            // 
            
            $listiners = htmlspecialchars(json_encode($this->getConfig("listners", [$inpname])));
    
            $this->setConfig("payload","let json =$listiners ;  return liveUpdateForm([],json,event.form,{'$inpname-qrystr':querystr}) ");
        
            $this->setConfig("onsuccess"," $('#$inpname').html($(response.form['$inpname']).find('#$inpname').html()); $('#$inpname').trigger('change')");
        }
        return $this;
    }
    function setupAjax($fn){
        
    }
   
    function registerJsComponent(){ 
        // if($this->getConfig("type")=="select")
        {
            return "function(component,config){ 
            setTimeout(()=>{
            
            
            let select = $(component).find('select');
            if(select && select.attr('onsearch')){
                 select.select2({
                                placeholder: select.data('placeholder'),
                                closeOnSelect: true,
                                multiple:select.attr('multiple') ? true : false,
                                ajax: {
                                cache: true,
                                     delay: 400,
                                    url: '',
                                    type:'GET',
                                    data: function (params) {
                                        var dynmaicParam  = {}
                                        if(select.data('payload')){
                                            let fn = new Function('event','querystr',select.data('payload'));
                                            dynmaicParam=  fn(select[0],params.term)
                                        }
                                         
                                        return dynmaicParam;
                                    },
                                    processResults: function (data, params) {
                                        let all = []
                                       
                                        
                                        $(data.form[select[0].name])
                                        .find('option')
                                        .each(function(index, element){
                                         console.log({index, element})
                                            all.push({id:$(element).val(),text:$(element).text()})
                                        })
                                        
                                        return {results:all };
                                    }
                                },
                            });
                }
                            else{
                             select.select2({
                                placeholder: select.data('placeholder'),
                                closeOnSelect: true,
                                multiple:select.attr('multiple') ? true : false});
                            }
                     },0)
             }";
        }
         
    }
    function onSaveModel($model){
        if($this->getConfig("type","text")=="file"  ){
            if($this->getValue() instanceof \Illuminate\Http\UploadedFile){
             $model->{$this->getConfig("name","")} =    $this->getValue()->store($this->getConfig("path","public"));
            }
            return $model;
        }
        return parent::onSaveModel($model);
    }
    function getRelationalOptions(){
        $relation        = $this->getConfig("relation",false);
        $inpname=$this->getConfig("name");
        if( $relation){  
            if($this->controller){
                
                $model = $this->controller->getModel();
                $id = "id";
                $title= "title";
                if(is_string($relation)){
                    $modelClass = $model->{$relation}()->getRelated();
                   
                    $query = function($q)use($inpname){
                        
                        return $q->
                        when(request()->has("$inpname-qrystr") && !empty(request()->has("$inpname-qrystr")),function($q)use($inpname){
                            $q->where("name","like","%".request()->get("$inpname-qrystr")."%");
                           })->
                        pluck("name","id");
                    };
                }
                else{
                    $rname = $relation["name"];
                    $modelClass = $model->{$rname}()->getRelated();
                    if(isset($relation["query"])){
                        $query =$relation["query"];
                    }
                    else{
                        $query = function($q)use($relation,$inpname){
                            
                           return $q->when(request()->has("$inpname-qrystr") && !empty(request()->has("$inpname-qrystr")),function($q)use($relation,$inpname){
                            $q->where($this->getOption($relation,"titleKey","name"),"like","%".request()->get("$inpname-qrystr")."%");
                           })->pluck($this->getOption($relation,"titleKey","name"),$this->getOption($relation,"idKey","id"));
                        };
                    } 
                    
                }
               
                $class = get_class($modelClass);
                
                return  $query($class::query());

            }
        }
        return [];
    }

    function getFilePreviewByType($name){
        if($this->getValue()==""){
            return "";
        }
        $prevType = $this->getConfig("previewType","image");
        if($prevType=="image"){
            $existing = new ImageComponent(["name"=>"img_$name"]);
            return  $existing->setValue($this->getValue())->render();
        }
        else{
            $existing = new LinkComponent(["name"=>"link_$name"]);
            return  $existing->setConfig("href",$this->getValue())->render();
        }
       
        
    }
    function view(){
        if($this->getConfig("livesearch",false)){
            $this->enableLiveUpdateSupport("livesearch",$this->getConfig("livesearch",false));
        }
        return parent::view();

    }
    function buildInput($name,$attrs){
        
        
        $type           = $this->getConfig("type","text");
        $warning ='';
        if($this->getConfig("relation",false)){
            $options  = $this->getRelationalOptions();
        }
        else{
            $optionsFn  = $this->getConfig("options",function(){
                return [];
            });
            if(!is_callable($optionsFn)){
                $options  = $optionsFn;
                // $warning = "<span style='color:red'>deprecated use of options</span>";
            }
            else{
                try{
                   
                 $options  = $optionsFn();
                } catch(\Exception $e){
                    dump($e);
                }
            }

        }
        // $options        = $this->getConfig("relation",false)?$this->getRelationalOptions():$this->getConfig("options",[]);
      
      
        $placeholder = $this->getConfig("placeholder",$this->getConfig("label",""));

        if($type=="textarea"){
           
            $input= \Form::textarea($name,$this->getValue(),$attrs);
        }
        else if($type=="password"){
            $input= \Form::password($name,$attrs);
        }
        else if($type=="url"){
            $input= \Form::url($name,$this->getValue(),$attrs);
        }
        else if($type=="email"){
            $input= \Form::email($name,$this->getValue(),$attrs);
        }

        else if($type=="number"){
            $input= \Form::number($name,$this->getValue(),$attrs);
        }
        else if($type=="date"){
            $input= \Form::date($name,$this->getValue(), $attrs);
        }
        else if($type=="datetime"){

            $input= \Form::datetimeLocal($name,$this->getValue(), $attrs);
        }
        else if($type=="select"){
            // $attr = $this->getConfig("attr",[]);
            

            $optionattr  = $this->getConfig("options-attr",[]);
            if($this->getConfig("multiple",false)){
                unset($attrs["placeholder"]);
                $attrs  =  $attrs + ["data-placeholder"=>$placeholder];
            }
            $str='';
            if(isset($attrs['class'])){
                $attrs['class'].=" select2-input";
            }
            else{
                $attrs['class']=" select2-input";
            }
            foreach($attrs as $a=>$v){
                 $str .="$a=\"$v\"";
            }
            $value = $this->getValue();
            // if(is_array($value)){
            //     $value = implode(",",$value);
            // }
            $select  = "<select $str name=\"$name\"  >";
            $select.="<option   value=''>Select</option>";
             
            foreach($options as $key=>$option){

                $stro="";
                if(isset($optionattr[$key])){
                        foreach($optionattr[$key] as $atr_name=>$val){
                            $stro .= "$atr_name='$val'";
                        }

                }
               
                if(is_array($value))
                    $selected =  in_array($key,$value) ?"selected":"";
                else{
                        $selected =  $key==$value ?"selected":"";
                }
                $select.="<option $selected $stro value='$key'>$option</option>";
            }

            $select.='</select>'.$warning;
            // $optionattr
            $input=$select;//\Form::select($name,$options,$this->getValue(), $attrs);
        }
        else if($type=="radio"){
            $radio ="";  
            foreach($options as $k=>$option)
            $radio  .=  \Form::radio($name,$k,$k==$this->getValue() ?true:false,$attrs) . " $option";
            $input= $radio;
        }
        else if($type=="color"){

            $input =\Form::color($name,$this->getValue(), $attrs);
        }
        else if($type=="hidden"){
            $this->setConfig("showLabel",false);
            $attrs['data-config']=$this->getValue();
            $input =\Form::hidden($name,$this->getValue(), $attrs);
        }
        else if($type=="file"){
           
            $input= \Form::file($name,  $attrs).$this->getFilePreviewByType($name);
        }

        else{
            
            $input =\Form::text($name,$this->getValue(), $attrs);
        }
        return $input;

    }



}


class LiveUpdateComponent {
    public $effectedFields=[];


}