<?php
namespace Aman5537jains\AbnCmsCRUD\Layouts;
 use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;

class MultiFormBuilder extends FormBuilder{

    public function init(){
        parent::init();

        if(!empty($this->controller) && $this->controller instanceof FormBuilder ){
            $this->setConfig("form",false);

            $parentModel = $this->controller->getModel();
            $relationName = \Str::camel($this->getConfig("name"));;

            $this->setModel($parentModel->{$relationName}()->getRelated());

             $parentModel->{$relationName};

        }
        else{
            // throw new \Exception("Invalid Parent Form Builder -> should be new RelationalFormBuilder(['name'=>'relation_name'],parentFormBuilderObject) ");
        }
    }
    function onSaveModel($model){


        if(!empty($this->getModel())){
            $class = get_class($this->getModel());
            $prevRecordDeleteStrategy =  $this->getConfig("onDeletePrevious",function($query){ return $query;});
            $relationName = \Str::camel($this->getConfig("name"));;

            $query = $model->{$relationName}();
            $prevRecordDeleteStrategy($query)->delete();

            $arrayOfInput =$this->getValue();

            $class = get_class($this->getModel());

            foreach($arrayOfInput as $input){

                foreach($input as $k=>$value){

                    if($this->hasField($k)){
                      $this->getField($k)->setData($input)->setValue($value);
                    }
                }
                $modelClass = new $class;
                $model->{$relationName}[]= parent::onSaveModel($modelClass);

            }


          return $model;
        }else{
            $arrayOfInput =$this->getValue();

            $newModel = get_class( $model);
            $newModel =new $newModel;

            $values =[];

            foreach($arrayOfInput as $input){

                foreach($input as $k=>$value){

                    if($this->hasField($k)){
                      $this->getField($k)->setData($input)->setValue($value);
                    }
                }

                $values[]= parent::onSaveModel($newModel)->toArray();

            }

            $model->{$this->getConfig("name")}=json_encode($values);
            return $model;
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

    function setValue($values){
        $this->value = $values;
        $arr=[];

        if(is_array($values) && count($values)>0){

            foreach($values as $value){
                foreach($value as $col=>$val){
                    $arr[$col][]=$val;
                    if($this->hasField($col)){

                        $this->getField($col)->setData($value)->setValue($val);
                    }
                }
            }

        }


        return $this;
    }

    function getValue(){
        return $this->value;
    }

    function beforeRender($cmp)
    {

        $fields = $this->getFields();

        $formName = $this->getConfig("name","form");
        $i=0;

        foreach($fields as $field){
            $attr = $field->getConfig("attr",[]);
            $name = $field->getConfig("name");
            $multiple = $field->getConfig("multiple",false);
            $attr["name"]="$formName"."[0]"."[".$name."]".($multiple?"[]":"");

            $field->setConfig("attr",$attr);
            $i++;
        }


    }



}
