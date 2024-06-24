<div class="section-container pad-top-85 pad-bottom-65">
    <div class="container">
        <div class="media-section-row w-100">
            <div class="row">
                @foreach($rows as $index=>$row)
                    <div onclick="openModal();currentSlide({{$index+1}})" class="col-lg-4 col-md-6 col-sm-12 col-12 mb-4">
                        {!! $row->render() !!}
                    </div>
                @endforeach




            </div>
        </div>

    </div>
</div>

<div id="myModal" class="modal-gallery">
    <div class="position-relative d-flex flex-column justify-content-between gallery-tablet h-100">
        <div class="w-100%">
            <div class="close-gallery cursor d-flex align-items-center" onclick="closeModal()">
                <svg class="me-3" xmlns="http://www.w3.org/2000/svg" width="14.288" height="14.288" viewBox="0 0 14.288 14.288">
                    <path id="Icon_ionic-md-close" data-name="Icon ionic-md-close" d="M21.812,8.952,20.383,7.523l-5.715,5.715L8.952,7.523,7.523,8.952l5.715,5.715L7.523,20.383l1.429,1.429L14.667,16.1l5.715,5.715,1.429-1.429L16.1,14.667Z" transform="translate(-7.523 -7.523)" fill="#fff"/>
                </svg> Close
            </div>

            <div class="modal-content-gallery">
                <div class="modal-mid-images">
                    <?php
                        $rowsCount = count($rows);
                    ?>
                    @foreach($rows as $index=>$row)
                        <div class="mySlides">
                            <div class="numbertext font-24 font-medium text-white">{{$index+1}} / {{$rowsCount}}</div>
                            {!! $row->render() !!}
                            {{-- <div class="Gallryimage_view">
                                <div class="img-outer-box">
                                    <img src="{{asset('public/assets-low/img/big-popupimg.jpg')}}" class="w-100" />
                                </div>
                                <div class="w-100 text-center pt-3">
                                    <span class="font-20 text-center w-100 font-medium text-white pt-2 pb-2"> Title 1 </span>
                                </div>
                            </div> --}}
                        </div>
                    @endforeach

                    <div class="mobile-show-icons">
                        <a class="prev" onclick="plusSlides(-1)"></a>
                        <a class="next" onclick="plusSlides(1)"></a>
                    </div>

                </div>
            </div>
        </div>


        <div class="w-100 tablet-hide">
            <div class="w-100 d-flex align-items-center popup-list-row">
                <ul>
                     @foreach($rows as $index=>$row)
                    <li onclick="currentSlide({{$index+1}})" class="popup-box-bottom"> 
                       <!--  <div class="popup-box-bottom">
                            <img class="cursor-pointer" src="{{asset('public/assets-low/img/galley1.jpg')}}"  alt="" />
                        </div> -->

                         {!!  $row !!}
                    </li>
                    @endforeach
                   

                </ul>
            </div>
        </div>

    </div>
</div>
