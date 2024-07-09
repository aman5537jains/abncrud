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
    public  static $classIds = [];

    public static function registerRoute(){
        return config("crud.routes");
    }

    public static  function registerJs($class,$js,$alpine,$id=''){

        if (is_string($class)){
            self::$allJs[$class]=["js"=>$js,"alpine"=>$alpine];
            // self::$classIds[$class]=$js;
        }
        else
        self::$allJs[get_class($class)]=["js"=>$js,"alpine"=>$alpine];;
    }

    public static function js()
    {
        if(!CrudService::$jsRegistered)   {
            CrudService::$jsRegistered=true;
            $allJS ='';
            $alpins = '';
            foreach(self::$allJs as $js){
                $allJS.=" \n " .$js["js"];
                $alpins.=" \n " .$js["alpine"];
            }
            $alpins="<script>
                document.addEventListener('alpine:init', () => {
                        $alpins
                })
            </script>";

            return $allJS.$alpins;
        }
        return "";
    }
    // public static function json()
    // {
    //     if(!CrudService::$jsRegistered)   {
    //         CrudService::$jsRegistered=true;
    //         $allJS ='';
    //         $alpins = '';
    //         foreach(self::$allJs as $js){
    //             $allJS.=" \n " .$js["js"];
    //             $alpins.=" \n " .$js["alpine"];
    //         }
    //         $alpins.="<script>  $alpins  </script>";

    //         return $allJS.$alpins;
    //     }
    //     return "";
    // }

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
        <script src='https://unpkg.com/alpinejs@3.14.0/dist/cdn.js' defer></script>
        <script>
        class CrudBuilderJS{
            components={};
            windowLoaded=false;
            queue=[];

            register(name,cb,id){
                if(!this.components[name]){
                    this.components[name]={id:""};
                }
                this.components[name][id]={cb:cb};
                this.components[name]['global_cb']=cb;
                console.log(id,"id")

            }
            isRegistered(name){
                // if(this.components,this.components[name.replaceAll("\\\","")]){
                //     this.components[name][id].element= element
                // }

                console.log( this.components,this.components[name.replaceAll("\\\","")]?"Registered":"NOT REgisterr",name)
                return this.components[name.replaceAll("\\\","")];
            }

            call(name,element,config='',id=''){
                if(config){
                    config=    JSON.parse(atob(config))
                    if(typeof config=="string")
                    config =JSON.parse(config)
                }


                if(this.windowLoaded){
                        if(this.components[name] && !this.components[name][id]){
                            this.components[name][id]={cb:this.components[name]['global_cb']};
                        }
                        this.components[name][id].element= element
                        this.components[name][id].config= config
                        this.components[name][id].id = id
                        this.components[name][id].name = name
                        this.components[name][id].methods = {}
                        this.components[name][id].cb(element,config,this.components[name][id]);



                }
                else{
                    this.queue.push({name,element,config,id});
                }

            }

            component(name,id=''){
                return this.components[name][id];
            }
            componentCB(name){
                return this.components[name]['global_cb'];
            }

            onLoadDocument(){
                window.onload =  () =>{
                    this.windowLoaded=true;
                        for(let cmp of this.queue){
                            this.components[cmp.name][cmp.id].element=cmp.element
                            this.components[cmp.name][cmp.id].config=cmp.config
                            this.components[cmp.name][cmp.id].id =cmp.id;
                            this.components[cmp.name][cmp.id].name =cmp.name
                            this.components[cmp.name][cmp.id].methods = {}
                            this.components[cmp.name][cmp.id].cb(cmp.element,cmp.config,this.components[cmp.name][cmp.id])
                        }
                }
            }
        }
        var  crudBuilderJS = new CrudBuilderJS();
        window['crudBuilderJS'] = crudBuilderJS;
        crudBuilderJS.onLoadDocument();
        window['crudBuilderJS'].alpines={};
        </script>
        rawJS;
    }

    static function renderComponent(){

        $class =request("class",false);
        if($class){
            $comp = new $class(["name"=>request("name","name")]);
        }
        else{
            $comp = new DynamicFormComponent(["name"=>request("name","name")]);
        }

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
        $ConfigBuilderComponent->setvalue((request("config")));
        // return $ConfigBuilderComponent->render();
        return $ConfigBuilderComponent->json();

    }
}
