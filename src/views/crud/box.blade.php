 
<form style="margin-top:10px" class="validate form-horizontal" method="GET" action="" enctype="multipart/form-data">
   <div class="row">
      <?php
      $per_page_arr = config("constants.ALL_PAGE");
      $per_page = !empty(request()->per_page)?request()->per_page:config("constants.PER_PAGE");
      
  ?>
  <div class="col-md-12 col-lg-12">
  <div class="form-group showFormGroup showFormGroup-01 ps-15px mb-0">
      <label for="inputEmail3" class="control-label shottxt">Show</label>
      <div class="showFormGroup-input paglists">
          {!! Form::select('per_page',$per_page_arr, $per_page, [ 'class'=>'form-control','id'=>'show','title'=>'Show entries','onchange'=>'this.form.submit()']) !!} 
      </div>
      <div class="entrylsit">
          <label for="inputEmail3" class="control-label">entries <span class="devider-mid">&nbsp; |&nbsp; </span> Total {{@$total}} entries</label>
      </div>
  </div>                            
  </div>
   </div>

</form>
<div style="margin-top:10px" class="listing banktable" style="overflow-x:auto">
    <div class ="row">
   
    @foreach($rows as $key=>$row)
    <div class="card col {{$component->getConfig('col',"col-4")}} border">
        @if(isset($mapper["image"]) && isset($row->{$mapper["image"]}))
            {!! $row->{$mapper["image"]}->render() !!}
        @endif
        <div class="card-body">
            <h5 class="card-title"> {!! $row->{$mapper["title"]}->render() !!}</h5>
            <h6 class="card-subtitle mb-2 text-muted">  {!! $row->{$mapper["sub_title"]}->render() !!}</h6>
            <p class="card-text">{!! $row->{$mapper["description"]}->render() !!}</p>
            <span> {!! $row->{$mapper["date"]}->render() !!}</span>
            <span> {!! $row->{$mapper["actions"]}->render() !!}</span>
        </div>
    </div>
    @endforeach
</div>
    
 </div>
 @if(count($rows)>0)
<div class="pagiingbottom">
            {!! $pagination !!}
</div>
@endif
    @if(count($rows)<=0)
    <div class="pagiingbottom">
                No Records
    </div>
 @endif
 
</div>
<style>
    .border{
        border:1px solid;
        margin:10px;
    }
    </style>