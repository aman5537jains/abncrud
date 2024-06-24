@extends('front.layouts.dashboard')

@section('content')

<div class="dbody">
   <div class="dbody-inner">
      @include('message')
      <div class="dashHead">
         <div class="dashHead-left">
            <h4 class="dashTitle">{{$module_title}}</h4>
            <!-- <p>Showing 1 to 10 of 150 entries</p> -->
         </div>
         <div class="dashHead-right">
            <div class="dashHead-action">
               <a class="buttons dbtn-secondary" href="{{$module}}/add"><i class="fas fa-plus-circle"></i>{{getAddNewButtonTect($module_title)}}</a>
            </div>
         </div>
      </div>
      <div class="listing">
         <table id="closed_orders_datatables">
            <thead>
               <tr class="list-row">
                  @foreach($fields as $field)
                  <th> {{$field->getLabel()}}</th>
                  @endforeach
               </tr>
               @foreach($rows as $row)
               <tr class="list-row">
                  @foreach($fields as $key=>$field)
                     <td> {{ $field->setValue($row->{$key})->render() }}</td>
                  @endforeach
               </tr>
               @endforeach
            </thead>
         </table>
      </div>
      
         @if(count($rows)>0)
         <div class="pagiingbottom">
                       {{ $rows->appends($_GET)->links() }}
                   </div>
                   @endif

      
   </div>
</div>
@endsection
@section('uniquepagescript')
 
@endsection
