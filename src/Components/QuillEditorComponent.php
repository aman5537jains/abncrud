<?php
namespace Aman5537jains\AbnCmsCRUD\Components;

use Aman5537jains\AbnCmsCRUD\FormComponent;

class QuillEditorComponent extends InputComponent{

    function registerJsComponent()
    {
        return "{

            init(){
                let elem  = this.\$el;
                const quill = new Quill(elem, {
                    theme: 'snow',
                    modules: {
                            'syntax': true,
                            'toolbar': [
                            [{ 'font': [] }, { 'size': [] }],
                            [ 'bold', 'italic', 'underline', 'strike' ],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'script': 'super' }, { 'script': 'sub' }],
                            [{ 'header': '1' }, { 'header': '2' }, 'blockquote', 'code-block' ],
                            [{ 'list': 'ordered' }, { 'list': 'bullet'}, { 'indent': '-1' }, { 'indent': '+1' }],
                            [ 'direction', { 'align': [] }],
                            [ 'link', 'image', 'video', 'formula' ],
                            [ 'clean' ]
                        ]
                    }
                });
                quill.on('text-change', (delta, oldDelta, source) => {
                    if (source == 'api') {
                        console.log('An API call triggered this change.',quill.root.innerHTML);
                    } else if (source == 'user') {
                        console.log('A user action triggered this change.',quill.root.innerHTML);
                    }
                });

            }

        }";

    }

    function js(){
        return'
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
        ';
    }
    function buildInput($name, $attrs)
    {
        return $this->getValue();
    }



}
