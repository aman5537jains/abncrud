<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use Illuminate\Support\Facades\Blade;
 


class FileInputComponent extends FormComponent{
    


    function setValue($value){
        if($value instanceof \Illuminate\Http\UploadedFile){
            $value =  $value->store($this->getConfig("path","files"));
        }
        return parent::setValue($value);
    }
    
    
    function view(){
        $name    = $this->config["name"];
        $validations = $this->validations();
        $required =false;
        foreach($validations as $validation){
            if($validation=="required")
            $required =true;
        }
        
         
        $placeholder = $this->config["placeholder"];
       
        $input= \Form::file($name, ['placeholder' => $placeholder, 'required'=>$required, 'class'=>'dForm-control']);
        
        return   '
        
            <div class="dForm-group">
                <label class="dForm-label">'.$this->getLabel().'<span class="mandatory">*</span></label>
                 '.$input.'
                 <img src="'.url("storage/app/".$this->getValue()).'" width=50 height=50 />
            </div>
       
        ';
        // return $input;
        
    }

}