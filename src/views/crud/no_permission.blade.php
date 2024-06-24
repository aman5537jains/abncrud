@extends('front.layouts.dashboard')

@section('content')

<div class="dbody">
   <div class="dbody-inner">
      @include('message')
      <div class="dashHead">
         <div class="dashHead-left">
            <h4 class="dashTitle">{{$module_title}}</h4>
         </div>
         <div class="dashHead-right">
            <div class="dashHead-action">
              
            </div>
         </div>
      </div>
    <h2>You don't have permission to access this module</h2>
    
</div>

@endsection
 
