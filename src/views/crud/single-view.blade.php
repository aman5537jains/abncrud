@extends('layouts.dashboard')

@section('content')

    <div class="dbody">
        <div class="dbody-inner">
        <div class="dashHead">
                <div class="dashHead-left">
                    <h4 class="dashTitle">
                    
                    <a href="{{route($module.'.index')}}" class="text-red font-22px me-2">
                            <svg class="position-relative top-4px" width="24" height="24" viewBox="0 0 23.816 23.816">
                               <use xlink:href="#arrow-back"></use>
                            </svg>
                         </a> 
                     View {{  singularize($module_title)}}</h4>
                </div>
            </div>
            <div class="dashBoard-tiles">
                <div class="dashBoard-tile">
                     
                    <div class="dForm">
                       
                        {!!  $single->render() !!}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
   
@endsection

@section('uniquepagescript') 

<?php 
echo \App\Lib\CRUD\CrudService::js();
?>

@endsection