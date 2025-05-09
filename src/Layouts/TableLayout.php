<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts;

use Aman5537jains\AbnCmsCRUD\Components\ChangeStatusComponent;
use Aman5537jains\AbnCmsCRUD\Components\ImageComponent;
use Aman5537jains\AbnCmsCRUD\Components\TextComponent;
use Aman5537jains\AbnCmsCRUD\Layout;

class TableLayout  extends Layout
{


    public function init(){
        parent::init();

     }

    function setModel($model){
      parent::setModel($model);

      if($this->getConfig("autoBuild",false))
          $this->viewFields($this);
        
       $this->setValue($this->search()->paginate(request("per_page","10")));
    }

    public function viewFields($builder){
      $arr=[];
      $view_columns = (config("crud.view_fields"));
      $model =$this->getModel();
      $cls =get_class($model->getModel());
      
      $columns=  \DB::select("SHOW COLUMNS FROM ". (new $cls)->getTable());
      foreach($columns as $key=>$col){
          $column = $col->Field;
          if($col->Extra!="auto_increment" && $col->Field!="updated_at" && $col->Field!="created_at" )
          {
              $class = TextComponent::class;
              $option=["validations"=>["required"]];

              $arr[$column]=["class"=>$class,"config"=>$option];
              if(isset($view_columns[$column])){
                  $arr[$column]=$view_columns[$column];
              }

              if($column=="status"){

                $arr[$column]=["class"=>ChangeStatusComponent::class,"config"=>["url"=>"",
                  "beforeRender"=>function($component){
                      $data = $component->getData();
                    //   $component->setConfig("url",$this->action("changeStatus",[$data["row"]->{$this->uniqueKey}]));
                  }]];
              }
              if (strpos($column, "image") !== false) {
                  $arr[$column]=["class"=>ImageComponent::class,"config"=>$option];
              }
              if (strpos($column, "thumb") !== false) {
                  $arr[$column]=["class"=>ImageComponent::class,"config"=>["height"=>50,"width"=>"50"]];
              }


          }
      }
    //   $arr['actions']=["class"=>ActionComponent::class,"config"=>["url"=>"",
    //                 "module"=>$this->getConfig("module",""),
    //                 "uniqueKey"=>$this->getConfig("uniqueKey","id"),"allowed"]
    //   ];
      foreach($arr as $fldName=>$opt){
          $builder->addField($fldName,$opt);
      }
      return $arr;

    }
      public function addColumnDefination($model,$values){
          $arr=[];
          $cls =get_class($model->getModel());
          $columns=  \DB::select("SHOW COLUMNS FROM ". (new $cls)->getTable());
          foreach($columns as $columnDef){
              $column = $columnDef->Field;
              if($column!="id"
                  && $column!="updated_at"
                  && $column!="slug"
                  && $column!="created_at"
              )
              {
                  $arr[$column]= isset($values->{$column}) ? $values->$column :"";
              }
          }

          return $arr;
      }

      function search(){
          $request    = request();
        //   $model      = $this->getModel();
          $model      = $this->getModel();

          $fields     = $this->addColumnDefination($model,(object)[]);

          if(!empty($request->input('search.value'))){
              $model      = $model->where(function($q)use($fields,$request){
                              foreach($fields as $field=>$val){
                                      $q->orWhere($field,"like","%".$request->input('search.value')."%");
                              }
                          });
          }
          $search  = $this->getConfig("search",function($q,$s){
              return $q;
          });
          // $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');



          $model = $search( $model,$request->input('search.value'));
          return  $model->orderBy("id","DESC");
      }
      function isPost(){
        return false;
      }

      function view(){
            $rows = $this->getValue();

            $total = $rows->total();
            return view("crud.table",["inputs"=>$this->inputLayout(),
                    "component"=>$this,
                    "fields"=>$this->getFields(),
                    'total'=>$total,
                    "searchComponent"=>$this->getConfig("searchComponent",new TextComponent(["name"=>"Search"])),
                    'pagination'=>$rows->appends($_GET)->links()
          ]);

        }
}
