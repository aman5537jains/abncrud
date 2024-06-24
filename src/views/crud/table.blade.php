
<form style="margin-top:10px" class="validate form-horizontal" method="GET" action="" enctype="multipart/form-data">
   <div class="row">
      <?php
      $per_page_arr = ['10'=>'10','20'=>'20','50'=>'50','100'=>'100'];
      $per_page = !empty(request()->per_page)?request()->per_page:10;

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

       {!!  Form::text('search[value]', request()->input("search.value",""), [ "style"=>"float:right;width:240px",'class'=>'dForm-control','id'=>'show','placeholder'=>'Type Keyword and Enter','onchange'=>'this.form.submit()'])  !!}
  </div>
  </div>
   </div>

</form>
<div style="margin-top:10px" class="listing banktable" style="overflow-x:auto">

    {!!  $inputs  !!}
 </div>

<div class="pagiingbottom">
            {!! $pagination !!}
</div>





</div>
