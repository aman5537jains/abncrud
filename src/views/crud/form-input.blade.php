<div class="row {{ $component->getConfig("layout-col","") }}">
@foreach ($fields as $key=>$field)
    @if($field->isVisible())
        <div class=" {!! $field->getConfig("col",$component->getConfig("input-layout-col","col-lg-12 col-md-12 col-sm-12 col-xs-12")) !!}">
             {!! $field->render() !!}
        </div>
    @endif
@endforeach
    </div>