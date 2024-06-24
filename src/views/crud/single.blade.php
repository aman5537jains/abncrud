
    @foreach ($fields as $key=>$field)

    <div class="row" style='padding: 10px;border: 1px solid #d1d1d1;'>
        <div class="col ">
            
            {!! $field->getLabel() !!}
       </div>
        <div class="col ">
            
             {!! $field->render() !!}
        </div>
    </div>  
    @endforeach
 