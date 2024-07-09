<?php


namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class PopupComponent extends ViewComponent{

    function registerJsComponent(){
        return "(component,config,componentObject)=>{
            console.log('clicked',component,config);
            let that = $(component);
            openPopup(component,config)

            component.setContent=function(content){
                console.log('content',$(that).find('.contentWrapper').eq(0),content);
                $(that).find('.contentWrapper').eq(0).html(content);
            }
        }";
    }

    function js(){

                return "
                <script>
                    function openPopup(component,config){
                        let that = $(component).find('.popup_cmp');
                        $(that).on('click',function(){
                            console.log('clicked');
                            $(component).find('.offcanvas-backdrop').addClass('show');
                            $(component).find('.viewContent').addClass('show');
                        })
                        $(component).find('.btnClose').on('click',function(){

                            $(component).find('.offcanvas-backdrop').removeClass('show');
                            $(component).find('.viewContent').removeClass('show');
                        })

                    }
                </script>

                ";

    }



    function view(){
        $content = $this->getConfig("content",'');
        $label = $this->getConfig("label",'Open');

        return <<<view
        <div>
            <button type='button' class='popup_cmp buttons secondary'>$label</button>
            <span class="offcanvas-backdrop"></span>
            <div class="offcanvas offcanvas-end max-w-600 w-100 px-0 viewContent" tabindex="-1"  >
            <button type="button" class="btnClose text-reset btn-close-1 Modal-site-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <svg width="11.267" height="11.267" viewBox="0 0 11.267 11.267">
                    <path id="Icon_ionic-md-close" data-name="Icon ionic-md-close" d="M18.791,8.65,17.664,7.523,13.157,12.03,8.65,7.523,7.523,8.65l4.507,4.507L7.523,17.664,8.65,18.791l4.507-4.507,4.507,4.507,1.127-1.127-4.507-4.507Z" transform="translate(-7.523 -7.523)" fill="#757575"></path>
                </svg>
            </button>

                <div id="contentWrapper" style='padding:20px' class='contentWrapper'>$content</div>

            </div>
        </div>
        view;
    }
}
