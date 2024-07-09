



      <div class="dashHead">
         <div class="dashHead-left">
            <h4 class="dashTitle">{{$module_title}}</h4>
         </div>
         <div class="dashHead-right">
            <div class="dashHead-action">
               @if($controller->hasPermission("add",$module,false) &&  $canAdd)
                 <a class="buttons dbtn-secondary" href="{{$controller->action("create")}}"><i class="fas fa-plus-circle"></i>{{'Add '.singularize($module_title)}}</a>
               @endif
            </div>
         </div>
      </div>
      @if(isset($search))
      {!! $search->render() !!}
      @endif
      {!! $table->render() !!}




<?php
// echo \App\Lib\CRUD\CrudService::js();
?>

