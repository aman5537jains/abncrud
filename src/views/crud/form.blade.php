
            <div class="dashHead">
                <div class="dashHead-left">

                    <h4 class="dashTitle">

                        <a style='float: left;' href="{{$controller->action("index")}}" class="text-red font-22px me-2">
                            <svg height="25px" id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" width="25px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M189.3,128.4L89,233.4c-6,5.8-9,13.7-9,22.4c0,8.7,3,16.5,9,22.4l100.3,105.4c11.9,12.5,31.3,12.5,43.2,0  c11.9-12.5,11.9-32.7,0-45.2L184.4,288h217c16.9,0,30.6-14.3,30.6-32c0-17.7-13.7-32-30.6-32h-217l48.2-50.4  c11.9-12.5,11.9-32.7,0-45.2C220.6,115.9,201.3,115.9,189.3,128.4z"/></svg>
                         </a>
                         {{ $form->getModel()->exists ? "Edit" : "Add"}} {{singularize($module_title)}}</h4>
                </div>

            </div>
            <div class="dashBoard-tiles">
                <div class="dashBoard-tile">
                    <div class="dForm">
                        {!!  $form->render() !!}
                    </div>
                </div>
            </div>



