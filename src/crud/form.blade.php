@extends('layouts.dashboard')

@section('content')

    <div class="dbody">
        <div class="dbody-inner">
            @include('message')
            <div class="dashHead">
                <div class="dashHead-left">
                    <h4 class="dashTitle">{{getAddNewButtonTect($module_title)}}</h4>
                </div>
            </div>

            <div class="dashBoard-tiles">
                <div class="dashBoard-tile">
                     
                    <div class="dForm">
                        <form class="validate form-horizontal form-form"  role="form" method="POST" action=" " enctype = "multipart/form-data" id="formAddCuisine">
                            {{ csrf_field() }}
                            
                            @foreach ($fields as $key=>$field)
                                
                          
                            <div class="row">
                                <div class="col col-md-4">
                                    <div class="dForm-group">
                                        <label class="dForm-label">{{$field->getLabel()}} <span class="mandatory">*</span></label>
                                         {!! $field->render() !!}
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <div class="row">
                                <div class="col">
                                    <div class="dForm-actions form-buttons">
                                         <a class="buttons grey" href="{{url($module)}}" name="button">Cancel</a>
                                        <button class="buttons secondary" type="submit" name="button">Save</button>
                                       
                                    </div>
                                    <div class="dForm-actions loader" style="display: none;">
                                        <a class="buttons grey" href="{{url($module)}}" name="button" style="float: left">Cancel</a>
                                        <a class="buttons secondary" href="javascript:;"><img src="{{url('public/asset/images/loader-white.gif')}}" class="loader_img"> </a>
                                        
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
@section('uniquepagescript') 

@endsection