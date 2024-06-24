<?php
namespace Aman5537jains\AbnCmsCRUD\Layouts;
 use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;

class RelationalFormBuilder extends FormBuilder{


    public function init(){
        parent::init();
        if(!empty($this->controller) && $this->controller instanceof FormBuilder ){
            $this->setConfig("form",false);
            $parentModel    = $this->controller->getModel();
            $relationName   = $this->getConfig("name");
            $this->setModel($parentModel->{$relationName}()->getRelated());
        }
        else{
            throw new \Exception("Invalid Parent Form Builder -> should be new RelationalFormBuilder(['name'=>'relation_name'],parentFormBuilderObject) ");
        }
    }

    function onSaveModel($parentModel)
    {

        $relationName = $this->getConfig("name");
        $arrayOfInput = $this->getValue(); // it should be array

        foreach($arrayOfInput as $input){
            foreach($input as $k=>$value){
                if($this->hasField($k)){
                    $this->getField($k)->setData($input)->setValue($value);
                }
            }
            $model = clone $this->getModel();

            $parentModel->{$relationName}[]= parent::onSaveModel($model);
        }


    }

    function validations()
    {
        $allRuleNew =[];
        $allRule = parent::validations();
        foreach($allRule as $key=>$rule){
            $allRuleNew[$this->getConfig("name").".*.$key"]=$rule;
        }
        return $allRuleNew;
    }


    function beforeRender($cmp)
    {
        $fields     = $this->getFields();
        $formName   = $this->getConfig("name","form");
        $i=0;
        foreach($fields as $key=>$field){
            $attr = $field->getConfig("attr",[]);
            $name = $field->getConfig("name");
            $attr["name"]="$formName"."[]"."[".$name."]";
            $field->setConfig("attr",$attr);
            $i++;
        }


    }



}
