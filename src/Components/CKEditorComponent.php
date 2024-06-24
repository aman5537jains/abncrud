<?php
namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;

class CKEditorComponent extends FormComponent{

    function registerJsComponent()
    {
        $url =url("/abn-cms/upload");
        $token =csrf_token();

        return "{

            init(){
                let elem  = this.\$el;
                ClassicEditor
	.create( elem, {
		simpleUpload: {
            // The URL that the images are uploaded to.
            uploadUrl: '$url',

            // Enable the XMLHttpRequest.withCredentials property.
            withCredentials: true,

            // Headers sent along with the XMLHttpRequest to the upload server.
            headers: {
                'X-CSRF-TOKEN': '$token',

            }
        }
		// Editor configuration.
	} )
	.then( editor => {
		window.editor = editor;
	} )
	.catch(function(e){

    } );



            }

        }";

    }

    function js(){
        $js = url("public/vendor/abncrud/js/ckeditor.js");
        return'
        <script src="'.$js.'"></script>

        ';
    }
    function parentContainer($view, $jsComponent)
    {
        $name =  $this->getAttribute("name");
        return "<textarea  $jsComponent name='$name' >$view</textarea>";
    }
    function view()
    {
        return $this->getValue();
    }



}
