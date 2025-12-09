<?php

namespace Aman5537jains\AbnCmsCRUD\Lib;
use Illuminate\Support\Facades\Route;;
use Illuminate\Support\Str;
class RouteService
{
    public $parentObject;
    public $routes=[];
    public $class;
    public $module;
    function __construct($object=null){
        $this->parentObject= $object;


    }

    function register($cb){
        // dump($this->parentObject->module);
        Route::prefix($this->parentObject->module)->name($this->parentObject->module.'.')->group(function () use($cb) {

            $cb($this);
            
            Route::get('/changeStatus/{id}',"\\".$this->parentObject->controllerClass."@changeStatus")->name("changeStatus");
            Route::get('/{id}/delete',"\\".$this->parentObject->controllerClass."@delete")->name("delete");
            
        });

        Route::resource($this->parentObject->module, "\\".$this->parentObject->controllerClass);
    }
    static function resource($module,$class,$cb){
        $RouteService = new RouteService((object)["controllerClass"=>$class,"module"=>$module]);
        $RouteService->register($cb);

    }

    function __call($name,$params){
        $action = $params[0];
        $path =   Str::snake($action, '-');

        $RouteName  =$action;
        if(isset($params[1])){
            $path = $params[1];
        }
        if(isset($params[2])){
            $RouteName = $params[2];
        }

        return Route::$name($path, "\\".$this->parentObject->controllerClass."@".$action)->name($action);
    }
}
