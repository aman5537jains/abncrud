<?php

namespace Aman5537jains\AbnCmsCRUD;
use App\Http\Controllers\Controller;
use Aman5537jains\AbnCmsCRUD\Components\ActionComponent;
use Aman5537jains\AbnCmsCRUD\Components\ChangeStatusComponent;
use Aman5537jains\AbnCmsCRUD\Components\ConfirmLinkComponent;
use Aman5537jains\AbnCmsCRUD\Components\FileInputComponent;
use Aman5537jains\AbnCmsCRUD\Components\ImageComponent;
use Aman5537jains\AbnCmsCRUD\Components\InputComponent;
use Aman5537jains\AbnCmsCRUD\Components\SubmitButtonComponent;
use Aman5537jains\AbnCmsCRUD\Components\TextComponent;
use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;
use Aman5537jains\AbnCmsCRUD\Layouts\SingleViewLayout;
use Aman5537jains\AbnCmsCRUD\Layouts\TableLayout;
use Aman5537jains\AbnCmsCRUD\Components\MultiComponent;
use Aman5537jains\AbnCmsCRUD\Components\LinkComponent;
use Aman5537jains\AbnCmsCRUD\Layouts\DataTableLayout;
use Aman5537jains\AbnCmsCRUD\Lib\RouteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use stdClass;
use Illuminate\Support\Facades\Route;;

class CrudController extends Controller
{
    public $model;
    public $view="crud.";
    public static $module="";
    public $uniqueKey="slug";
    public $columns=[];
    public static $moduleTitle='';
    public $controllerClass;
    public $controllerName;
    public $layouts=[];
    public $theme="AbnCmsCrud::";
    public $canAdd=true;
    public $config=true;
    public $routeService;
    public $clousures;

    public function __construct($config=[])
    {

        $this->config =    $config;
        $this->model =    $this->getModel();

        if(count($config)>0){
            foreach($config as $key=>$value){
                if(isset($this->{$key})){
                    $this->{$key} =$value;
                }
            }
        }

        $this->routeService= new \Aman5537jains\AbnCmsCRUD\Lib\RouteService($this);
        $this->init();
    }

    public function addButtonTitle(){
        return 'Add '.singularize(static::$moduleTitle);
    }


    public function assets(){
        return "";
    }
    public function action($name,$params=[],$module=null){
        if($name==''){
            return url(($module ==null ? static::$module : $module));
        }
        return route(($module ==null ? static::$module:$module).".".$name,$params);
    }


    public static function route(Request $request,$name,$action,$slug){
        $CrudController =new CrudController;
        $CrudController->$name($CrudController);
    }

    public  function link($name="",$inner='<span class="caret"></span>'){
        if($this->hasPermission("view",static::$module,false))
            return "<li><a class='test btnRuning' href='".route(static::$module.".index") ."'> ".static::$moduleTitle." $inner </a> </li>";
        else
            return '';
    }

    // public function permissions(){
    //     static::$module=>['view' => 'view', 'add' => 'add', 'edit' => 'edit','delete' => 'delete',"options"=>["label"=>__t(static::$module)]];

    // }
   function flash($message,$type="info"){

   }

    static function resource(){
        // dd(static::class);
        $class =static::class;
        $class::$module;

        $RouteService = new RouteService((object)["controllerClass"=>$class,"module"=>$class::$module]);
        $RouteService->register(function($service){
            static::getRoutes($service);
        });

    }
    public  function routes(){

        $this->routeService->register(function($service){

            $this->getRoutes($service);
        });
    }
    public static function getRoutes($RouteService=null){

    }



    function getController(){
        $this->controllerClass  = get_class($this);
        $splitted               = explode("\\",$this->controllerClass);
        $this->controllerName  = $splitted[count($splitted)-1];
    }

    function describeTable($table){
        return \DB::select("SHOW COLUMNS FROM ". $table);
    }
    protected function model(){
        $modelName              = str_replace("Controller","",$this->controllerName);
        $modelPath              = "\App\Models" ;//config("crudconfig.model_path");
        $this->model           = $modelPath."\\".$modelName;
        if(static::$moduleTitle=='')
            static::$moduleTitle = ucfirst($modelName);
        if(static::$module=='')
            static::$module =$modelName ;
        return $this->model;
    }
    public function init(){

        // $this->setView();
        $this->getController();
        // $this->model();
        $this->model = $this->getModel();

    }

    public function setModel($model){
           $this->model =$model;
           return $this;
     }
    public function getModel(){
       return  $this->model;
    }
    public function getModelObject(){
        return new  $this->model;
    }
    public function getModelTable(){
        return (new $this->model)->getTable();
    }

    public function addColumnDefination($model,$values){
        $arr=[];

        $columns=  $this->describeTable($model->getTable());
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



    public function formFields($builder){
        $arr=[];
        $model =$this->getModel();
        $columns=  $this->describeTable((new $model)->getTable());
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
        $arr['submit']=["class"=>SubmitButtonComponent::class,"config"=>["label"=>"Save","url"=>$this->action("index"),"saveDB"=>false]];
        foreach($arr as $fldName=>$opt){
            $builder->addField($fldName,$opt);
        }
        //     // Field
        //     // Type
        //     // Null
        //     // Extra
        //     // Default
        //     // Key
        // }


        return $arr;



    }
    public function viewFields($builder){
        $arr=[];
        $view_columns = (config("crud.view_fields"));
        $model =$this->getModel();
        $columns=  $this->describeTable((new $model)->getTable());
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

                if($column=="status" && $this->hasPermission("edit",static::$module,false)){

                  $arr[$column]=["class"=>ChangeStatusComponent::class,"config"=>["url"=>"",
                    "beforeRender"=>function($component){
                        $data = $component->getData();
                        $component->setConfig("url",$this->action("changeStatus",[$data["row"]->{$this->uniqueKey}]));
                    }]];
                }
                else{
                    $arr[$column]=["class"=>TextComponent::class];
                }
                if (strpos($column, "image") !== false) {
                    $arr[$column]=["class"=>ImageComponent::class,"config"=>$option];
                }
                if (strpos($column, "thumb") !== false) {
                    $arr[$column]=["class"=>ImageComponent::class,"config"=>["height"=>50,"width"=>"50"]];
                }


            }
        }
        $components = [];
        if($this->hasPermission("edit")){
            $components[]= new LinkComponent(["link"=>"", "beforeRender"=>function($component){
                $data = $component->getData();
                $component->setConfig("link",$this->action("edit",[$data["row"]->{$this->uniqueKey}]));
             },
             "label"=>'<svg enable-background="new 0 0 512 512" height="18" viewBox="0 -1 401.52289 401" width="18" xmlns="http://www.w3.org/2000/svg"><g fill="#666"><path d="m370.589844 250.972656c-5.523438 0-10 4.476563-10 10v88.789063c-.019532 16.5625-13.4375 29.984375-30 30h-280.589844c-16.5625-.015625-29.980469-13.4375-30-30v-260.589844c.019531-16.558594 13.4375-29.980469 30-30h88.789062c5.523438 0 10-4.476563 10-10 0-5.519531-4.476562-10-10-10h-88.789062c-27.601562.03125-49.96875 22.398437-50 50v260.59375c.03125 27.601563 22.398438 49.96875 50 50h280.589844c27.601562-.03125 49.96875-22.398437 50-50v-88.792969c0-5.523437-4.476563-10-10-10zm0 0" xmlns="http://www.w3.org/2000/svg"/><path d="m376.628906 13.441406c-17.574218-17.574218-46.066406-17.574218-63.640625 0l-178.40625 178.40625c-1.222656 1.222656-2.105469 2.738282-2.566406 4.402344l-23.460937 84.699219c-.964844 3.472656.015624 7.191406 2.5625 9.742187 2.550781 2.546875 6.269531 3.527344 9.742187 2.566406l84.699219-23.464843c1.664062-.460938 3.179687-1.34375 4.402344-2.566407l178.402343-178.410156c17.546875-17.585937 17.546875-46.054687 0-63.640625zm-220.257812 184.90625 146.011718-146.015625 47.089844 47.089844-146.015625 146.015625zm-9.40625 18.875 37.621094 37.625-52.039063 14.417969zm227.257812-142.546875-10.605468 10.605469-47.09375-47.09375 10.609374-10.605469c9.761719-9.761719 25.589844-9.761719 35.351563 0l11.738281 11.734375c9.746094 9.773438 9.746094 25.589844 0 35.359375zm0 0" xmlns="http://www.w3.org/2000/svg"/></g></svg>']
            );
        }
        if(count($components)>0){
            $arr['actions']=["class"=>MultiComponent::class,"config"=>["components"=>  $components
            ]];
        }
        foreach($arr as $fldName=>$opt){
            $builder->addField($fldName,$opt);
        }
        return $arr;

    }
    public function canAdd(){
        return $this->canAdd;
    }
    public function view($name,$arr=[]){
        $arr["module"]=static::$module;
        $arr["assets"]=$this->assets();
        $arr["canAdd"]=$this->canAdd();
        $arr["controller"]=$this;
        $arr["theme"]=$this->theme;
        $arr["module_title"]=static::$moduleTitle==null?ucfirst(static::$module):static::$moduleTitle;

        return view(($this->theme==""?"":$this->theme.".").$this->view.".".$name,$arr);
    }


    public function search($model,$q){

        return  $model;

    }
    public static function permissions(){
          $class =static::class;
          return [$class::$module=> ['view' => 'view','add' => 'add', 'edit' => 'edit','status' => 'status','delete' => 'delete',
          "options"=>["label"=>static::$moduleTitle]
          ]];
    }
    public function getPermissions(){
        return [];
    }
    public function hasPermission($action,$module="",$redirect=true){

        $permission =  $this->getPermissions();
        if($module=="")
           $module=static::$module;
        if(isset($permission[static::$module.'___'.$action]) || $permission=="superadmin")
        {
            return true;
        }

        if($redirect){

             echo  view("crud.no_permission",["module_title"=>static::$moduleTitle])->render();
             die;
        }
        else{
            return false;
        }
    }

    public function hasPermissions($permissions){
            if(!$permissions){
                Session::flash('warning', getErrorMessages('1'));
                return redirect()->back();
            }
            return true;
    }

    public function viewBuilder($model){

        $this->layouts["table"] = $TableLayout =  (new TableLayout(["searchUrl"=>$this->action("index"),
                "autoBuild"=>true,"search"=>function($model,$q){
            return $this->search($model,$q);
        }],$this));
        $TableLayout->setModel($model);

        $components = [];
        if($this->hasPermission("edit",static::$module,false)){
            $components[]= new LinkComponent(["name"=>"edit","link"=>"", "beforeRender"=>function($component){
                $data = $component->getData();

                $component->setConfig("link",$this->action("edit",[$data["row"]->{$this->uniqueKey}]));
             },
             "label"=>'<svg enable-background="new 0 0 512 512" height="18" viewBox="0 -1 401.52289 401" width="18" xmlns="http://www.w3.org/2000/svg"><g fill="#666"><path d="m370.589844 250.972656c-5.523438 0-10 4.476563-10 10v88.789063c-.019532 16.5625-13.4375 29.984375-30 30h-280.589844c-16.5625-.015625-29.980469-13.4375-30-30v-260.589844c.019531-16.558594 13.4375-29.980469 30-30h88.789062c5.523438 0 10-4.476563 10-10 0-5.519531-4.476562-10-10-10h-88.789062c-27.601562.03125-49.96875 22.398437-50 50v260.59375c.03125 27.601563 22.398438 49.96875 50 50h280.589844c27.601562-.03125 49.96875-22.398437 50-50v-88.792969c0-5.523437-4.476563-10-10-10zm0 0" xmlns="http://www.w3.org/2000/svg"/><path d="m376.628906 13.441406c-17.574218-17.574218-46.066406-17.574218-63.640625 0l-178.40625 178.40625c-1.222656 1.222656-2.105469 2.738282-2.566406 4.402344l-23.460937 84.699219c-.964844 3.472656.015624 7.191406 2.5625 9.742187 2.550781 2.546875 6.269531 3.527344 9.742187 2.566406l84.699219-23.464843c1.664062-.460938 3.179687-1.34375 4.402344-2.566407l178.402343-178.410156c17.546875-17.585937 17.546875-46.054687 0-63.640625zm-220.257812 184.90625 146.011718-146.015625 47.089844 47.089844-146.015625 146.015625zm-9.40625 18.875 37.621094 37.625-52.039063 14.417969zm227.257812-142.546875-10.605468 10.605469-47.09375-47.09375 10.609374-10.605469c9.761719-9.761719 25.589844-9.761719 35.351563 0l11.738281 11.734375c9.746094 9.773438 9.746094 25.589844 0 35.359375zm0 0" xmlns="http://www.w3.org/2000/svg"/></g></svg>']
            );
        }
        if($this->hasPermission("delete",static::$module,false)){
            $components[]= new ConfirmLinkComponent(["link"=>"", "beforeRender"=>function($component){
                $data = $component->getData();

                $component->setConfig("link",$this->action("delete",[$data["row"]->{$this->uniqueKey}]));
             },
             "label"=>'<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="18" height="18" viewBox="0 0 30 30">
             <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
         </svg>']
            );
        }

             if($TableLayout->hasField("status")){
                $permission = $this->hasPermission("edit",static::$module,false);

                $TableLayout->setField("status",["class"=>ChangeStatusComponent::class,"config"=>["url"=>"",
                "beforeRender"=>function($component)use($permission){
                    if($permission){
                        $data = $component->getData();
                        $component->setConfig("url",$this->action("changeStatus",[$data["row"]->{$this->uniqueKey}]));
                    }
                    else{
                        $component->setConfig("url","javascript:;");
                    }
                }]]);

              }



        if(count($components)>0){
            $TableLayout->addField("actions",["class"=>MultiComponent::class,"config"=>["components"=> $components]]);
        }
        if($TableLayout->isPost()){
            return $TableLayout->sendJson();
        }
        return $TableLayout;
    }

    public function index(Request $request,$slug=""){

            $this->hasPermission("view");
            $TableLayout =$this->viewBuilder($this->getModelObject());
            return  $this->view("index",["table"=>$TableLayout]);
    }

    public function formBuilder($model=null){

        $this->layouts["form"] = $form =  (new FormBuilder(["module"=>static::$module,"back_url"=>$this->action("index")],$this));
        $form->setModel($model);
        $this->formFields($form);
        return $form;
    }

    public function create(Request $request,$slug=""){
        if(!$this->canAdd())
            return  "Not allowed";
        $this->hasPermission("add");
        return $this->view("form",["form"=>$this->formBuilder($this->getModelObject())
        ->setConfig("action",$this->action("store"))]);
    }

    function store(){
        $this->hasPermission("add");
        $form =$this->formBuilder($this->getModelObject())->setConfig("action",$this->action("store"));

        $response =  $form->validateAndSave(request()->all());

        if($response->status){
            $this->flash("Record Added Successfully");
            return redirect($this->action("index"));
        }
        else{
            $this->flash($response->data->first(),"danger");
            return redirect()->back()->with(["errors"=>$response->data])->withInput();
        }
    }

    public function edit(Request $request,$slug=""){
        $this->hasPermission("edit");
        $model=$this->getModel();
        $form =$this->formBuilder($this->getModelObject()->where($this->uniqueKey,$slug)->first())
        ->setConfig("action",$this->action("update",[$slug]));
        if($slug!=""){
             
            $form =$form->setValue(array_merge($form->getModel()->toArray(),old()));
        }

       return $this->view("form",["form"=>$form]);

    }

    public function update(Request $request,$slug=""){

        $this->hasPermission("edit");

        $response = $this->formBuilder($this->getModelObject()
        ->where($this->uniqueKey,$slug)->first())
        ->setConfig("action",$this->action("update",[$slug]));
// dd(request()->all());
        $response = $response->validateAndSave(request()->all());

        if($response->status){
            $this->flash("Record updated Successfully");
            return redirect($this->action("index"));
        }
        else{

            $this->flash($response->data->first(),"danger");
            return redirect()->back()->withInput()->with(["errors"=>$response->data]);;
        }

    }
    public function afterSave($form,$model){

    }
    public function delete(Request $request,$slug){
        $this->hasPermission("delete");
        $model = new $this->model;
        $model->where($this->uniqueKey,$slug)->delete();
        $this->flash("Record deleted Successfully");
        return redirect($this->action("index"));
    }
    public function changeStatus(Request $request,$slug){
        $this->hasPermission("edit");
        $model = new $this->model;
        $module = $model->where($this->uniqueKey,$slug)->first();
        $module->status = $module->status=='1'?"0":'1';
        $module->save();
        $this->flash("Record changed Successfully");

        return redirect($this->action("index"));
    }

    public function singleViewFields($builder){
        $model =$this->getModel();
        $columns=  $this->describeTable((new $model)->getTable());
        $view_columns = (config("crud.view_fields"));
        foreach($columns as $column){
            if(isset($view_columns[$column->Field])){
            $builder->addField($column->Field,$view_columns[$column->Field]);
            }
            else
            $builder->addField($column->Field,new TextComponent(["name"=>$column->Field],$this));
        }
    }

    public function show(Request $request,$slug){
        $this->hasPermission("view");
        $model = new $this->model;
        $module = $model->where($this->uniqueKey,$slug)->first();
        $single = (new SingleViewLayout([],$this));
        $this->singleViewFields($single);
        $single->setValue($module);
        return $this->view("single-view",["single"=>$single]);

    }

}


