<?php
namespace Aman5537jains\AbnCmsCRUD\Components;
use Aman5537jains\AbnCmsCRUD\FormComponent;
use Aman5537jains\AbnCmsCRUD\ViewComponent;

class CounterAnimationComponent extends ViewComponent{

    function jsConfig()
    {
        return [ "duration"=>$this->getConfig("duration",3000)];
    }

    function registerJsComponent(){
        return "(component,config)=>{
            let activated=false;
            $(component).inViewport(function(px) {
             
                console.log( px ); // `px` represents the amount of visible height
                if(px > 0) {
                    if(activated){
                        return true;
                    }
                    activated=true;
                    if($(this).data('rendered')=='COMPLETE' || $(this).data('rendered')=='INPROGRESS')
                    return 1;
                   
                    let that =$(this);
                    let counter =that.text();
                    that.data('rendered','INPROGRESS');
                    jQuery({ Counter: 0 }).animate({ Counter: counter }, {
                        duration: config.duration,
                        easing: 'swing',
                        step: function (now,tween) {
                            // console.log(now,JSON.stringify(tween));
                            if(now==counter){
                                that.data('rendered','COMPLETE');
                                that.text(counter+'+');
                            }
                            else
                           that.text(Math.ceil(this.Counter));
                        }
                      });
                }else{
                  // do that if element exits  the viewport // px = 0
                }
              });
        }";
    }

    function js(){
        return <<<script
        <script>
      
        (function($, win) {
           
            $.fn.inViewport = function(cb) {
               return this.each(function(i,el) {
                 function visPx(){
                   var elH = $(el).outerHeight(),
                       H = $(win).height(),
                       r = el.getBoundingClientRect(), t=r.top, b=r.bottom;
                   return cb.call(el, Math.max(0, t>0? Math.min(elH, H-t) : Math.min(b, H)));  
                 }
                 visPx();
                 $(win).on("resize scroll", visPx);
               });
            };
          }(jQuery, window));
          </script>
        script;
    }
    function view(){
        return "<span>".$this->getValue()."</span>";
    }
}