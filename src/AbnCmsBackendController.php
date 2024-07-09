<?php

namespace Aman5537jains\AbnCmsCRUD;

use AbnCms\RolesPermission\PermissionService;
use Aman5537jains\AbnCms\Lib\AbnCms;
use Aman5537jains\AbnCms\Lib\Theme\ScriptLoader;

class AbnCmsBackendController extends CrudController
{
   public function __construct()
   {
        // $theme = AbnCms::getActiveTheme("BACKEND_ACTIVE_THEME");
        // $this->theme = $theme->getKey();
        parent::__construct();
   }
   function flash($message,$type="info"){
         AbnCms::flash($message,$type);
   }

   public function hasPermission($action,$module="",$redirect=true){


        if(!PermissionService::has(($module==''?static::$module:$module),$action)){
            if($redirect){
                return abort(403);
            }
            else{
                return false;
            }
        }
        return true;

    }
   public function view($name,$arr=[]){
        $arr["module"]=static::$module;
        $arr["assets"]=$this->assets();
        $arr["canAdd"]=$this->canAdd();
        $arr["controller"]=$this;
        $arr["theme"]=$this->theme;
        $arr["module_title"]=static::$moduleTitle==null?ucfirst(static::$module):static::$moduleTitle;

        $view =  view(($this->theme==""?"":$this->theme).$this->view.$name,$arr);
        $rendered = $view->render();


        return AbnCms::getActiveTheme("BACKEND_ACTIVE_THEME")

        ->setPageContent($rendered)->render();

    }
    public function viewHtml($html,$arr=[]){
        $arr["module"]=static::$module;
        $arr["assets"]=$this->assets();
        $arr["canAdd"]=$this->canAdd();
        $arr["controller"]=$this;
        $arr["theme"]=$this->theme;
        $arr["module_title"]=static::$moduleTitle==null?ucfirst(static::$module):static::$moduleTitle;


        $rendered = $html;


        return AbnCms::getActiveTheme("BACKEND_ACTIVE_THEME")
      
        ->setPageContent($rendered)->render();

    }
}


