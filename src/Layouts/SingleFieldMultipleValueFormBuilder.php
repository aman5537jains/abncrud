<?php
namespace Aman5537jains\AbnCmsCRUD\Layouts;
 use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;

class SingleFieldMultipleValueFormBuilder extends FormBuilder{

    public $relation;
    public function init(){

        parent::init();

        if(!empty($this->controller) && $this->controller instanceof FormBuilder ){
            $this->setConfig("form",false);

            $parentModel = $this->controller->getModel();
            $relationName = \Str::camel($this->getConfig("name"));;

            $this->setModel($parentModel->{$relationName}()->getRelated());

            $this->relation = $parentModel->{$relationName};


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

            foreach($arrayOfInput as $name=>$input){

                foreach($input as $k=>$value){

                    if($this->hasField($name)){
                      $this->getField($name)->setData($input)->setValue($value);
                    }
                    $modelClass = new $class;
                    $model->{$relationName}[]= parent::onSaveModel($modelClass);
                }


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

    function beforeRender($cmp)
    {
        $fields = $this->getFields();
        $keys = array_keys($fields);

        $field = $fields[$keys[0]];
        $formName = $this->getConfig("name","form");


        $attr = $field->getConfig("attr",[]);
        $name = $field->getConfig("name");
        $multiple = $field->getConfig("multiple",false);
        $attr["name"]="$formName"."[".$name."]".($multiple?"[]":"");
        $field->setConfig("attr",$attr);

        if($this->relation->count()>0){

            $field->setValue($this->relation->pluck($name)->toArray());
        }


    }


}
