@if($component->getConfig('form',true))

<form   {!!  $component->getConfig('onsubmit',"")  !!} role="form" method="{{$component->getConfig('method','POST')}}" action="{{$component->getConfig('action','')}}" enctype = "multipart/form-data" id="{{ $component->getConfig('name',"default-form") }}"   {!!  $component->getAttributesString('onsubmit',"")  !!}>
 @endif
   @if($component->getConfig('method','POST')=="POST" && $component->getConfig('form',true))
    {{ csrf_field() }}
    @endif

    @if( $component->getConfig('form',true) && $component->getModel() && $component->getModel()->exists)
     <input type="hidden" name="_method" value="PATCH" />
    @endif


        {!! $fields !!}


@if($component->getConfig('form',true))
</form>
@endif
