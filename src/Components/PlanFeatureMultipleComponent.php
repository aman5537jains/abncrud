<?php 

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
use function GuzzleHttp\json_encode;

class PlanFeatureMultipleComponent extends FormComponent{
    
   

    function js(){
        
        
                return "
                
                <script>
               
            </script>
                ";
        
    }
    function onSaveModel($model){

        $model->{$this->getConfig("name","")} = json_encode($this->getValue());
        return $model;
    }
   
    function view(){
        $json = json_decode($this->getValue());
 
        return '
             <div class="dForm-group">
                 <h2 class="dForm-label">'.$this->getLabel().' '.$this->requiredSpan().'</h2>
                  
                <div style="border:1px solid;border-radius:5px;padding:10px">
                  Sms '. \Form::number("features[sms]",@$json->sms, ['placeholder' => "Sms Count", 'class'=>'  dForm-control']).' <br> 
                  Email '. \Form::number("features[email]",@$json->email, ['placeholder' => "Email Count", 'class'=>'  dForm-control']).' <br> 
                  Storage '. \Form::number("features[storage]",@$json->storage, ['placeholder' => "Storage Count in MB", 'class'=>'  dForm-control']).' <br> 
                </div>
                 
             </div>
         ';
 
        // return "<input type='$type' name='$name' placeholder='$placeholder' />";
    }

}