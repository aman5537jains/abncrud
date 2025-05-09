<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

 
use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class RepeatableComponent extends FormBuilder{
    public $form;
    public $forms=[];

    function init(){
        parent::init();
        $this->form = new RepeatableFormComponent(["name"=>$this->getConfig("name"),"counter"=>"0"]);
        
    }
    function setValue($values){
       
        if($this->getConfig("saveMethod","JSON")=="JSON"){
                try{
                    
                    $values =json_decode($values,true);
                }
                catch(\Exception $e){
                     
                }
            
        }
        $this->forms=[];
        try{
            foreach($values as $k=>$value){
                    $form =    $this->createNewForm($k,$value);
                    $this->forms["$k"] = $form;
            }
        }
        catch(\Exception $e){

        }
       return $this;
       
    }

    function getValue(){
        $values =[];
        
        foreach($this->forms as $k =>$form){
            if($k!=="{{counter}}")
            {
                $values[$k] = $form->getValue();
            }
          
            
        }
        return $values;
    }
     
    function onSaveModel($model){
        if($this->getConfig("saveMethod","JSON")=="JSON")
            $model->{$this->getConfig("name")} = json_encode($this->getValue());
        else if($this->getConfig("saveMethod","JSON")=="RELATIONAL"){
            $relation = $this->getConfig("relation");
            $modelClass = $model->{$relation}()->getRelated();
            $class = get_class($modelClass);
            $ids = [];  
            // dd($this->forms);
            foreach($this->forms as $key=>$value){
                $idValue= $value->getField("id")->getValue();
               
                if($idValue>0){
                    $ids[]=$idValue;
                }
               $model->{$relation}[]=$value->onSaveModel( $idValue>0 ?   $class::find($idValue):new $class);
            }
            
            if(count($ids)>0){
                
                $model->{$relation}()->whereNotIn("id",$ids)->delete();
            }
             
        }
        
        return $model;
    }
    function validations(){
        $formName = $this->getConfig("name");
        $rules = $this->form->validations();
       
        $newRule = [];
        foreach($rules as $key=>$rule){
            $newRule[$formName.".*.".$key] =$rule;
        }
        return $newRule;
    }
    function validationMessages()
    {
        $formName = $this->getConfig("name");
        $rules = $this->form->validationMessages();
    
        $newRule = [];
        foreach($rules as $key=>$rule){
            $newRule[$formName.".*.".$key] =$rule;
        }
        return $newRule;

        
    }
    
    function js(){
        return "<script>
        function removeForm(e){
            $(e).parent().remove();
        }
                    function addMore(){
                            
                            let html = atob($('#repeatable-clone').val()).replace(/repeatable{{counter}}/g, 'repeatable');

                            $('.repeatable-forms').append(html.replace(/{{counter}}/g, $('.repeatable').last().data('counter')+1))
                    }
        </script>";
    }

    function addDefaultIfNotExist(){
        if(count($this->forms)==0){
            $form =   $this->createNewForm(0);
 
            $this->forms[]= $form;
        }
    }

    function createNewForm($counter,$value=[]){
        $form =    new RepeatableFormComponent(["name"=>$this->getConfig("name"),"counter"=>$counter]);
        foreach($this->form->getFields() as $name=>$field){
             
            $form->addField($name,clone $field); 
        }    
        $form->setValue($value);
        return $form;
    }

    function view(){
        $this->addDefaultIfNotExist();
        $cloneForm  = $this->createNewForm("{{counter}}",[]);
        $cloneForm ->setConfig("counter","{{counter}}");
        $formsList='';
        
        foreach($this->forms as $key=>$form){
            $form =str_replace("{{counter}}",$key,$form);

           $formsList.= '<div style="    border: 1px dotted;  padding: 10px; border-radius: 10px; margin: 13px 0;"  class ="repeatable" data-counter="'.$key.'"  ><span style="cursor:pointer;z-index:99;position: absolute;
    right: 35px;" type="button" onclick="removeForm(this)">X</span> '. $form. '  </div>';
        }
        $html = '<div style="border: 1px dotted; padding: 10px;  border-radius: 10px; margin: 13px 0;" class="repeatable" data-counter="{{counter}}">
             <span style="cursor:pointer;z-index:99;position: absolute; right: 35px;" type="button" onclick="removeForm(this)">X</span>
                '.$cloneForm. '
                 </div>
            </div>';
        return  '<div style="    border: 1px solid;
    padding: 10px;
    border-radius: 10px;
    margin: 13px 0;" class="repeatable-container" >
            <h2>'.$this->getLabel().'</h2>
            <div class="repeatable-forms">'.$formsList.'
            </div>
            <button type="button" class="buttons secondary" onclick="addMore()">Add More</button>
            <input type="hidden" id="repeatable-clone" value="'.base64_encode($html).'" />
        </div>';
    }

}

class RepeatableFormComponent extends FormBuilder{

    function init(){
        parent::init();
        $this->setConfig("form",false);
        $this->addField("id",new HiddenComponent(["name"=>"id"]));
    }
    function setFormNameArray(){
        $formName = $this->getConfig("name");
        $counter = $this->getConfig("counter",'0');
        foreach ($this->getFields() as $key => $value) {
            $name = $value->getConfig("name");
            $value->addAttributes([
            "name"=>"$formName"."[$counter][".$name."]",
            "data-validation-key"=>"$formName.$counter.$name",
            "data-key"=>"$name",
            "data-form-name"=>"$formName",
            "data-counter"=>"$counter"]);
        }
    }

    function view(){
        $this->setFormNameArray();
 
        return  parent::view();
    }
}