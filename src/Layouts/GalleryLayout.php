<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts;

use Aman5537jains\AbnCmsCRUD\Layout;

class GalleryLayout  extends Layout
{
    function registerJsComponent(){
            return "(component,config)=>{

                    initGallery(component,config);

            }";
    }
    function js(){
        return <<<script
            <script>
                function initGallery(component,config){

                }

                function openModal() {
                        document.getElementById("myModal").style.display = "block";
                }

                function closeModal() {
                    document.getElementById("myModal").style.display = "none";
                }

                var slideIndex = 1;
                showSlides(slideIndex);

                function plusSlides(n) {
                showSlides(slideIndex += n);
                }

                function currentSlide(n) {
                showSlides(slideIndex = n);
                }

                function showSlides(n) {
                    var i;
                    var slides = document.getElementsByClassName("mySlides");
                    var dots = document.getElementsByClassName("demo");
                    var captionText = document.getElementById("caption");
                    if (n > slides.length) {slideIndex = 1}
                    if (n < 1) {slideIndex = slides.length}
                    for (i = 0; i < slides.length; i++) {
                        slides[i].style.display = "none";
                    }
                    for (i = 0; i < dots.length; i++) {
                        dots[i].className = dots[i].className.replace(" active", "");
                    }
                    slides[slideIndex-1].style.display = "block";
                    dots[slideIndex-1].className += " active";
                    captionText.innerHTML = dots[slideIndex-1].alt;
                }
                </script>
        script;
    }


  function view(){
// dd(1,$this->processedResults);
        return view("AbnCmsCrud::gallery",["rows"=>$this->processedResults,
                "fields"=>$this->getFields(),
                "component"=>$this
      ]);

    }
}
