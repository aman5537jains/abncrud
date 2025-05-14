<?php
namespace Aman5537jains\AbnCmsCRUD;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Aman5537jains\AbnCmsCRUD\Components\ConfigBuilderComponent;

class CrudService{
    public  static $jsRegistered = false;
    public  static $allJs = [];
    public  static $classIds = [];

    public static function registerRoute(){
        return config("crud.routes");
    }

    public static  function registerJs($class,$js,$once,$alpine,$id=''){

        if (is_string($class)){
            if(isset(self::$allJs[$class])){
                self::$allJs[$class]["js"].=$js;
            }
            else
            self::$allJs[$class]=["js"=>$js,"once"=>$once,"alpine"=>$alpine];
            // self::$classIds[$class]=$js;
        }
        else
        self::$allJs[get_class($class)]=["js"=>$js,"once"=>$once,"alpine"=>$alpine];;
    }

    public static function js()
    {
        if(!CrudService::$jsRegistered)   {
            CrudService::$jsRegistered=true;
            $allJS ='';
            $once = '<style>.crud-wrapper{display: contents}</style>';
            $alpins = '';
            foreach(self::$allJs as $js){
                $once.=" \n " .$js["once"];
                $allJS.=" \n " .$js["js"];
                
                $alpins.=" \n " .$js["alpine"];

            }
            $loader = '<svg style="height:25px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><radialGradient id="a2" cx=".66" fx=".66" cy=".3125" fy=".3125" gradientTransform="scale(1.5)"><stop offset="0" stop-color="#FF156D"></stop><stop offset=".3" stop-color="#FF156D" stop-opacity=".9"></stop><stop offset=".6" stop-color="#FF156D" stop-opacity=".6"></stop><stop offset=".8" stop-color="#FF156D" stop-opacity=".3"></stop><stop offset="1" stop-color="#FF156D" stop-opacity="0"></stop></radialGradient><circle transform-origin="center" fill="none" stroke="url(#a2)" stroke-width="15" stroke-linecap="round" stroke-dasharray="200 1000" stroke-dashoffset="0" cx="100" cy="100" r="70"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="2" values="360;0" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></circle><circle transform-origin="center" fill="none" opacity=".2" stroke="#FF156D" stroke-width="15" stroke-linecap="round" cx="100" cy="100" r="70"></circle></svg>';
            
            $alpins="<script>
                window['crudBuilderJS']={alpines:{}};
                $alpins
                document.addEventListener('alpine:init', () => {
                        
                })
                //  document.querySelectorAll('[oninit]').forEach((el) => {
                //     const code = el.getAttribute('oninit');
                //     if (code) {
                //         try {
                         
                //         new Function('\$event', code)(el);
                //         // el.removeAttribute('oninit');
                //         } catch (e) {
                //          console.error('oninit error:', e, 'in element:', el);
                //         }
                //     }
                // });
     document.addEventListener('DOMContentLoaded', function () {
                const observer = new MutationObserver((mutationsList) => {
                         document.querySelectorAll('[oninit]').forEach((el) => {
                            const code = el.getAttribute('oninit');
                              el.removeAttribute('oninit');
                            if (code) {
                                try {
                                var fn =  new Function('\$event', code);
                               
                                fn(el)
                                } catch (e) {
                                console.error('oninit error:', e, 'in element:', el);
                                }
                            }
                        });
                   
                    });
                observer.observe(document.body, { childList: true, subtree: true });
        });

                function formData(obj){
                    let form_data = new FormData();
                    for ( var key in obj ) {
                        form_data.append(key, obj[key]);
                    }
                    return form_data;
                }
               function liveUpdateForm(emitter,listners,form,extra={}){
                 let formValues = {};
                 
                    for(let [key,value] of (new FormData(form)).entries()){
                        if(value instanceof File){
                            continue;
                         
                        }
                        formValues[key] = value;
                    }
                   return {...formValues,live_listners:listners,...extra}
                     
                }
                   
                 
                function liveUpdate(emitter,listners,form){
                    for(lisner of listners){
                       let id = $('#'+lisner).length>0 ? ($('#'+lisner).attr('id')+'-container') : lisner+'-container';
                        $('#'+id).replaceWith(form[lisner]);
                    }
                }
                function debounce(func, delay) {
                    let timer;
                    return function (...args) {
                        clearTimeout(timer);
                        timer = setTimeout(() => func.apply(this, args), delay);
                    };
                }
                function runCrudAjax(event,e){
                
                    if(event)
                    event.preventDefault();
                    if( $(e).attr('disabled')){
                        return;
                    }
                    let fnPayload = function(e){ return {} }
                   
                    if($(e).data('payload'))
                    {    
                        fnPayload =  new Function('event','formData',$(e).data('payload'));
                    }
                    let payload = fnPayload(e,formData);
                    console.log({payload:JSON.stringify(payload)})
                    
                     if($(e).data('beforesend'))
                                {   
                                   let fnBefore =  new Function('event',$(e).data('beforesend'));
                                   fnBefore(e);
                    }
                    $.ajax({
                            url:  $(e).data('href'),
                            type: $(e).data('method') || 'GET',
                            beforeSend:function(xhr){
                                
                                // $(e).attr('disabled','true');
                                $(e).parent().append('<div class=\"c_loader\">$loader</div>')
                                
                                 
                            },
                            data: payload,
                            cache: false,
                            processData: ($(e).data('method') || 'GET')=='GET'?true:false,
                            contentType: false,
                                headers: {
                                'Accept': 'application/json'
                            },
                            success: function (response) {
                                if($(e).data('onsuccess')){
                                    let fn =  new Function('event','response',$(e).data('onsuccess'));
                                    fn(e,response);
                                }
                            },
                            complete:function(xhr,status){

                                $(e).removeAttr('disabled');
                                 $(e).parent().find('.c_loader').remove();  
                            },  
                            error:function(xhr){
                                 if($(e).data('onerror')){
                                    let fn =  new Function('event','response',$(e).data('onerror'));
                                    fn(e,xhr);
                                }
                            }})

                }
                 
            </script>";

            return $once.$allJS.$alpins;
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
            // $comp = new DynamicFormComponent(["name"=>request("name","name")]);
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
