<?php
namespace Aman5537jains\AbnCmsCRUD\Components;
use Aman5537jains\AbnCmsCRUD\FormComponent;

class DateTimeRangePickerComponent extends FormComponent{


    function registerJsComponent(){
        return "{

            init(){
                let elem  = this.\$el;
                initDatePicker(elem,this.config)
            }
        }";
    }

    function js(){
        return <<<script

            <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
            <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
            <script>

            function initDatePicker(component,conf){

                let that = $(component).find('.date_range_picker');
                let config =  $(that).data("config");
                let onchange =  $(that).data("onchange");
                config = {...config, locale: {
                    format: 'YYYY-MM-DD'
                  }}
                $(that).daterangepicker(config,function(start,end,label){

                    try{
                    //  window["datePickerChange"+onchange](start,end,label);
                    }
                    catch(err){
                        throw err;
                    }
                });
            }

            </script>

        script;
    }
    function getValue()
    {
        $map =  $this->getConfig("map_column",[
            "from_date"=>"from_date",
            "to_date"=>"to_date"
            ]);
        $row = $this->getData();
        $pos = strpos(" - ",$this->value);

        if($pos===false){
            if( $row && isset($row[$map['from_date']] )){
                $this->value = $row[$map['from_date']]." - ".$row[$map['to_date']];
            }
        }

       return $this->value;
    }

    function onSaveModel($model)
    {  $map =  $this->getConfig("map_column",[
        "from_date"=>"from_date",
        "to_date"=>"to_date"
        ]);

        $value = $this->getValue();
        $values  = explode(" - ",$value);

        $model->{$map['from_date']} = trim($values[0]) ;
        $model->{$map['to_date']} =trim( $values[1]) ;
        return $model;
    }
    function buildInput($name,$attrs){


        $attrs['class'] .=  " date_range_picker";
        $attrs      = $attrs + ["data-config"=>json_encode($this->getConfig("config","{}")),"data-onchange"=>$this->getConfig("onChange","")];

        return \Form::text($name,$this->getValue(), $attrs);

    }
}
