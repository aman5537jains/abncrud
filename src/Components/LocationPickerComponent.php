<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;
class LocationPickerComponent extends FormComponent{


     function getValue(){
         $valNew = [];
         $columns = $this->getConfig("columns",["address"=>"address","lat"=>"lat","lng"=>"lng"]) ;
         $data =  $this->getData();
         if($data){
            $valNew['address'] = $data[$columns['address']] ;
            $valNew['lat']     = $data[$columns['lat']] ;
            $valNew['lng']     = $data[$columns['lng']] ;
         }
         else{
            $valNew['address'] = '' ;
            $valNew['lat']     = '' ;
            $valNew['lng']     = '' ;

         }
        return $valNew;

     }

     function onSaveModel($model){
         $columns    = $this->getConfig("columns",["address"=>"address","lat"=>"lat","lng"=>"lng"]) ;
         $val        = $this->getValue();
         $model->{$columns['address']} = $val['address'];
         $model->{$columns['lat']}     = $val['lat'];
         $model->{$columns['lng']}     = $val['lng'];
         return $model;
     }

     function js(){
        $value = $this->getValue();

        $key = config('constants.GOOGLE_MAP_KEY');
        $lat  = $value['lat'];
        $lng = $value['lng'];
        return <<<script

                        <script type="text/javascript">

                           function initWidget(){

                              let widgets = document.querySelectorAll(".location_picker_widget");
                              for(widget of widgets){
                                 let searchInput = widget.querySelector(".physical_address");
                                 let map = widget.querySelector(".mymap");
                                 let latitude = widget.querySelector(".latitude");
                                 let longitude = widget.querySelector(".longitude");

                                 initialize(searchInput,latitude,longitude,map)
                              }

                           }

                           function initialize(input,latitude,longitude,mapInput) {
                              var map;
                              var marker;
                              var lat = '$lat';
                              var lng = '$lng';
                              if(lat!=""){

                                  var latlng = new google.maps.LatLng(lat,lng);
                                  map = new google.maps.Map(mapInput, {
                                    center: latlng,
                                    zoom: 17
                                 });
                                  marker = new google.maps.Marker({
                                    map: map,
                                    position: latlng,
                                    draggable: true,
                                    anchorPoint: new google.maps.Point(0, -29)
                                 });
                              }
                              else{

                                 map = new google.maps.Map(mapInput, {

                                   zoom: 17
                                });
                                 marker = new google.maps.Marker({
                                   map: map,
                                   draggable: true,
                                   anchorPoint: new google.maps.Point(0, -29)
                                });
                              }
                                 var geocoder = new google.maps.Geocoder();

                                 //var options = {componentRestrictions: { country: "ke" }};
                                 var options = {
                                    componentRestrictions: false
                                 };

                                 var autocomplete = new google.maps.places.Autocomplete(input, options);
                                 autocomplete.bindTo('bounds', map);
                                 var infowindow = new google.maps.InfoWindow();
                                 autocomplete.addListener('place_changed', function() {
                                    mapInput.style.display = '';

                                    infowindow.close();
                                    marker.setVisible(false);
                                    var place = autocomplete.getPlace();
                                    if (!place.geometry) {
                                       window.alert("Autocomplete's returned place contains no geometry");
                                       return;
                                    }

                                    // If the place has a geometry, then present it on a map.
                                    if (place.geometry.viewport) {
                                       map.fitBounds(place.geometry.viewport);
                                    } else {
                                       map.setCenter(place.geometry.location);
                                       map.setZoom(17);
                                    }

                                    marker.setPosition(place.geometry.location);
                                    marker.setVisible(true);

                                    bindDataToForm(place.formatted_address, place.geometry.location.lat(), place.geometry.location.lng());
                                    infowindow.setContent(place.formatted_address);
                                    infowindow.open(map, marker);

                                 });
                                 // this function will work on marker move event into map
                                 google.maps.event.addListener(marker, 'dragend', function() {
                                    geocoder.geocode({
                                       'latLng': marker.getPosition()
                                    }, function(results, status) {
                                       if (status == google.maps.GeocoderStatus.OK) {
                                          if (results[0]) {

                                             console.log(results[0]);
                                             bindDataToForm(results[0].formatted_address, marker.getPosition().lat(), marker.getPosition().lng());
                                             infowindow.setContent(results[0].formatted_address);
                                             infowindow.open(map, marker);
                                          }
                                       }
                                    });
                                 });
                                 function bindDataToForm(address, lat, lng) {
                                    input.value = address;
                                    latitude.value = lat;
                                    longitude.value = lng;
                                 }
                              }

                        </script>
                        <script src="https://maps.googleapis.com/maps/api/js?key=$key&callback=initWidget"></script>
        script;
     }

     function view(){

      $val = $this->getValue();
      $display = $val['address']==''?"display:none":"";
      $add = \Form::text('address',$val['address'], ['placeholder' => __t('physical_address'),  'class'=>'dForm-control physical_address','maxlength'=>'255','id'=>'searchInput','required'=>true]);
      $lat = \Form::text('lat',$val['lat'], ['placeholder' => __t('latitude'),'class'=>'dForm-control latitude','maxlength'=>'255', 'readonly'=>true,'id'=>'lat']);
      $lng = \Form::text('lng',$val['lng'], ['placeholder' => __t('longitude'),'class'=>'dForm-control longitude','maxlength'=>'255','readonly'=>true,'id'=>'lng']);

      return <<<HTML
          <div class="location_picker_widget">
            <div class="row">
                            <div class="col col-md-8">
                               <div class="dForm-group">
                                  <label class="dForm-label">Physical address <span>*</span></label>
                                  $add
                               </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col col-md-4">
                               <div class="dForm-group">
                                  <label class="dForm-label">Latitude</label>
                                  $lat
                               </div>
                            </div>

                             <div class="col col-md-4">
                               <div class="dForm-group">
                                  <label class="dForm-label">Longitude</label>
                                 $lng
                               </div>
                            </div>
                        </div>

                        <div class="mymap" id="map" style="width: 66%; height: 300px;$display"></div>
                   </div>
      HTML;
     }

}
