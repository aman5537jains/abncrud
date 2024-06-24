<?php


namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class PopupComponent extends ViewComponent{


    // function registerJsComponent(){
    //     return "(element,config,component)=>{
    //         // if(component.isOpen==null){
    //         //     component.isOpen = false;
    //         //     component.setContent=function(content){
    //         //         component.modelContent = content;
    //         //     }
    //         //     component.setModel=function(status){
    //         //         component.isOpen = status;
    //         //     };

    //         // }

    //     }";
    // }




    function view(){
        $content = $this->getConfig("content",'');
        $label = $this->getConfig("label",'Open');

        return <<<view
        <div>
            <button type='button' class='popup_cmp buttons secondary' @click='setModel(true)'>$label</button>
            <span :class="isOpen? 'show offcanvas-backdrop': 'offcanvas-backdrop'"></span>
            <div :class="isOpen? 'show offcanvas offcanvas-end max-w-600 w-100 px-0 viewContent': 'offcanvas offcanvas-end max-w-600 w-100 px-0 viewContent'" tabindex="-1"  >
                <button type="button" class="btnClose text-reset btn-close-1 Modal-site-close" data-bs-dismiss="offcanvas"  @click='setModel(false)' aria-label="Close">
                    <svg width="11.267" height="11.267" viewBox="0 0 11.267 11.267">
                        <path id="Icon_ionic-md-close" data-name="Icon ionic-md-close" d="M18.791,8.65,17.664,7.523,13.157,12.03,8.65,7.523,7.523,8.65l4.507,4.507L7.523,17.664,8.65,18.791l4.507-4.507,4.507,4.507,1.127-1.127-4.507-4.507Z" transform="translate(-7.523 -7.523)" fill="#757575"></path>
                    </svg>
                </button>
                <template x-if="modelContent!=''">
                    <div id="contentWrapper" x-html='modelContent' style='padding:20px' class='contentWrapper'></div>
                </template>
                <template x-if="modelContent==''">
                    <div id="contentWrapper" style='padding:20px' class='contentWrapper'>$content</div>
                </template>
            </div>
        </div>
        view;
    }
}
