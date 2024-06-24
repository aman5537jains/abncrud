<?php

namespace Aman5537jains\AbnCmsCRUD;
use App\Http\Controllers\Controller;
use Aman5537jains\AbnCmsCRUD\Components\InputComponent;
use Aman5537jains\AbnCmsCRUD\Components\FileInputComponent;
use Aman5537jains\AbnCmsCRUD\FormComponent;

use Aman5537jains\AbnCmsCRUD\Layouts\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;;

class CrudBuilderController extends CrudController
{
    public static $module= "crud-builder";
    public function getModel(){
        return '';
    }
    public function hasPermission($action, $module = "", $redirect = true)
    {
            return true;
    }
    public static function getRoutes($RouteService=null){


        $RouteService->get("getTableConfig","getTableConfig");
        $RouteService->get("getComponents","getComponents");
        $RouteService->post("generateController","generateController");
        $RouteService->post("generateModel","generateModel");

        // Route::get( self::$module.'/getTableConfig',[CrudBuilderController::class,"getTableConfig"])->name(self::$module.".getTableConfig");
        // Route::get(self::$module.'/getComponents',[CrudBuilderController::class,"getComponents"])->name(self::$module.".getComponents");
        // Route::post(self::$module.'/generateController',[CrudBuilderController::class,"generateController"])->name(self::$module.".generateController");
        // Route::post(self::$module.'/saveController',[CrudBuilderController::class,"saveController"])->name(self::$module.".saveController");

        // Route::post(self::$module.'/generateModel',[CrudBuilderController::class,"generateModel"])->name(self::$module.".generateModel");
        // Route::post(self::$module.'/saveModel',[CrudBuilderController::class,"saveModel"])->name(self::$module.".saveModel");




    }
    public function create(Request $request,$slug=null){

        $tables = \DB::select('SHOW TABLES');

        $options = [];
        foreach($tables as $table)
        {
            foreach ($table as $key => $value)
                $options[$value] =   $value;

        }
        $url = url(self::$module) ;
       $formBuilder =  new FormBuilder(["module"=>self::$module,"action"=>"store","attr"],$this);



       $formBuilder->addField("table",(new InputComponent(["type"=>"select",
       "options"=>$options,
       "name"=>"table",
       "componentName"=>"tables","attr"=>["@change"=>"getTable()","x-model"=>"form.table"]])));

       $formBuilder->addField("module",new InputComponent(["name"=>"module","attr"=>["x-model"=>"form.module"]]));
       $formBuilder->addField("title",new InputComponent(["name"=>"title","attr"=>["x-model"=>"form.title"]]));
       $formBuilder->addField("model",new InputComponent(["name"=>"model","attr"=>["x-model"=>"form.model"]]));
       $formBuilder->addField("controller_path",new InputComponent(["name"=>"controller_path","attr"=>["x-model"=>"form.controller_path"]]));
       $formBuilder->addField("view_path",new InputComponent(["name"=>"view_path","attr"=>["x-model"=>"form.view_path"]]));
       $formBuilder->addField("columns",new DynamicTableColumn(["name"=>"columns"]));



        // dd($form->render() ,CrudService::js());
       return view("crud.builder.form-builder",['form'=>$formBuilder,"url"=>$url]);
    }

    public function getTableConfig(Request $request){

       $model_name = Str::singular(Str::studly($request->table));
       $model = "\\App\Models\\".$model_name;
       $title = Str::title(str_replace("_"," ",$request->table));
       $module = str_replace("_","-",$request->table);
       $controller_name = $model_name."Controller";
       $controller_path = app_path("Http/Controllers/");
       $view_path = resource_path("views");
       $sql = "SHOW COLUMNS FROM $request->table";
       $columns = $this->formFieldsNew( \DB::select($sql));

       return compact('model','title','module','controller_path','view_path','columns','controller_name','model_name');
    }

    function formFieldsNew($columns){
        $final=[];
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

             // $option["validations"]=$column->Null=="NO"?["required"]:[];
                $arr[$column->Field]=["class"=>$class,"config"=>$option];

                if(isset($formcolumns[$column->Field])){
                    $arr[$column->Field]=$formcolumns[$column->Field];
                }
                if(!isset($arr[$column->Field]['config']["name"])){
                    $arr[$column->Field]['config']["name"]=$column->Field;
                }
                if(!isset($arr[$column->Field]['config']["label"])){
                    $arr[$column->Field]['config']["label"]=ucfirst(str_replace("_"," ",$column->Field));
                }

                $final [] =  $arr[$column->Field]+  ["column"=>$column->Field];
            }
        }
        return $final;
    }

    function getComponents(){
        return config("crud.components");
    }
    function var_export($expression, $return=FALSE) {
        if (!is_array($expression)) return var_export($expression, $return);
        $export = var_export($expression, TRUE);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(["["] + $array));
        if ((bool)$return) return $export; else echo $export;
    }

    function generateController(Request $request){

        $controller = file_get_contents(app_path("Lib/CRUD/templates/controller.tmp"));
        $controller = str_replace("CONTROLLER_NAME",$request->controller_name,$controller);
        $controller = str_replace("MODULE_NAME",$request->module,$controller);
        $controller = str_replace("UNIQUE_KEY","id",$controller);
        $controller = str_replace("MODULE_TITLE",$request->title,$controller);
        $controller = str_replace("MODEL_CLASS",$request->model,$controller);
        $controller = str_replace("MODULE_TITLE",$request->title,$controller);
        $fields="";
        foreach($request->columns as $col){
             unset($col["column"]);
               $fields.= '
        $form->addField("'.$col["config"]['name'].'",'.$this->var_export($col,true).');';
        }
        $controller = str_replace("FORM_FIELDS",$fields,$controller);
        $controller = preg_replace("/^/m", "    ", $controller);

       return $controller;

    }
    function generateModel(Request $request){
        $model = file_get_contents(app_path("Lib/CRUD/templates/model.tmp"));
        $model = str_replace("MODEL_NAME",$request->model_name,$model);
        return $model;
    }

    function saveController(Request $request){
        if(file_exists($request->controller_path.$request->controller_name.".php")){
            return "Controller Already Exist";
        }
        if($request->code["controller"]!=""){
            file_put_contents($request->controller_path.$request->controller_name.".php",$request->code["controller"]);
        }
    }
    function saveModel(){
        if(file_exists(app("Models")."/".$request->model_name.".php")){
            return "Model Already Exist";
        }
        file_put_contents( app("Models")."/".$request->model_name.".php",$request->code["model"]);
    }
}

class DynamicTableColumn extends FormComponent{

    function js(){
        return '';
    }

    function view(){
        return view("crud.builder.component-setting")->render();
    }
}
