<?php
namespace Aman5537jains\AbnCmsCRUD;

use Aman5537jains\AbnCmsCRUD\Components\ConfigBuilderComponent;
use Aman5537jains\AbnCmsCRUD\Components\CounterAnimationComponent;
use Aman5537jains\AbnDynamicContentPlugin\Components\DynamicFormComponent;
use Aman5537jains\AbnDynamicContentPlugin\Components\DynamicViewComponent;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class CrudService{
    public  static $jsRegistered = false;
    public  static $allJs = [];

    public static function registerRoute(){
        return config("crud.routes");
    }

    public static  function registerJs($class,$js){

        if (is_string($class)){
            self::$allJs[$class]=$js;
        }
        else
        self::$allJs[get_class($class)]=$js;
    }

    public static function js()
    {
        if(!CrudService::$jsRegistered)   {
            CrudService::$jsRegistered=true;
            return implode(" \n " , array_values(self::$allJs));
        }
        return "";
    }
    public static function json()
    {
        if(!CrudService::$jsRegistered)   {
            CrudService::$jsRegistered=true;
            return self::$allJs;
        }
        return "";
    }

    public static function route($url)
    {
        Route::any($url,function(Request $request,$name,$action,$slug=""){

            $routes =  self::registerRoute();
            $app = app();
            if($routes[$name] instanceOf Controller){
                $controller = $routes[$name];
            }
            else
                $controller = $app->make( $routes[$name]);

            return $controller->{$action}($request,$slug);
        });

    }
    public static function routes(){
        $routes =  config("crud.modules");
        foreach($routes as $routeClass){

            $routeClass::resource();
        }
    }
    public static function permissions(){
        $routes =  config("crud.modules");
        $permissions = [];
        foreach($routes as $routeClass){
            $permissions+= $routeClass::permissions();
        }
        return $permissions;
    }
    public static function links(){
        $routes =  config("crud.modules");
        $permissions = [];
        foreach($routes as $routeClass){
            $permissions[]= $routeClass::links();
        }
        return $permissions;
    }

    public static function rawJs(){
        return <<<rawJS
        <script>
        class CrudBuilderJS{
            components={};
            windowLoaded=false;
            queue=[];

            register(name,cb){
                this.components[name]={cb:cb};


            }
            isRegistered(name){
                return this.components[name];
            }

            call(name,element,config='',id=''){
                if(config){
                    config=    JSON.parse(atob(config))
                    if(typeof config=="string")
                    config =JSON.parse(config)
                }


                if(this.windowLoaded){
                    this.components[name].cb(element,config,this.components[name]);
                    this.components[name].id =id

                }
                else{
                    this.queue.push({name,element,config});
                }

            }

            onLoadDocument(){
                window.onload =  () =>{
                    this.windowLoaded=true;



                        for(let cmp of this.queue){

                            this.components[cmp.name].cb(cmp.element,cmp.config,this.components[cmp.name])
                        }


                }
            }
        }
        var  crudBuilderJS = new CrudBuilderJS();
        window['crudBuilderJS'] = crudBuilderJS;
        crudBuilderJS.onLoadDocument();
        </script>
        rawJS;
    }

    static function renderComponent(){


        $comp = new DynamicFormComponent(["name"=>request("name","name")]);

        $configs  = $comp->defaultConfig();
        $components  = $comp->configComponents();
        $fields = [];
        foreach($configs as $config=>$value){
            if(isset($components[$config])){
                $fields[$config] = $components[$config];

            }
            else{
                $fields[$config] =$value;
            }

        }


        $ConfigBuilderComponent = new ConfigBuilderComponent(["name"=>request("name","name"),"fields"=>$fields]);
        $rendered  =$ConfigBuilderComponent->render();


        return [

            "html"=>$rendered,
            "js"=>CrudService::$allJs
        ];
    }
}
