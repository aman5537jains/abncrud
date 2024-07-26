<?php

namespace Aman5537jains\AbnCmsCRUD\Components;

use Intervention\Image\Facades\Image;
use Aman5537jains\AbnCmsCRUD\FormComponent;


class FileInputComponent extends FormComponent{

    function validations(){

        if(!empty($this->getValue()) || request()->isMethod("PATCH")  || request()->isMethod("POST") ){

            $this->validator()->remove(["required"]);

        }

        return $this->validator()->getValidations();
   }
    function registerJsComponent(){
        return "{
        init(){
          fileComponentInit(this.\$el,config)
        }
             }";
    }

    function js(){

        return "<script> function fileComponentInit(component,config){
                var fileBuffer = new DataTransfer();
                let fileInput = $(component).find('input[type=file]')[0];
                let prevContainer = $(component).find('.img-preview-container')[0];


                    fileInput.addEventListener('change',function(){
                        if(fileInput.hasAttribute('multiple')){

                            for (let i = 0; i < fileInput.files.length; i++) {
                                fileBuffer.items.add(fileInput.files[i]);

                                previewImg(fileInput,i);
                            }
                        }
                        else{

                            let i=0;
                            fileBuffer = new DataTransfer();
                            fileBuffer.items.add(fileInput.files[i]);
                            $(component).find('.preview-cont').remove();

                                previewImg(fileInput,i);



                        }
                        fileInput.files = fileBuffer.files;
                    });

                    function previewImg(fileInput,i){
                        const prevParent = document.createElement('div');
                        let imgElement ;
                        if(config.isImage){
                              imgElement = document.createElement('img');
                            imgElement.src = URL.createObjectURL(fileInput.files[i]);
                        }
                        else{

                                 imgElement = document.createElement('span');
                                imgElement.innerHTML = fileInput.files[i].name

                        }
                        prevParent.index_count=i;
                        prevParent.className='preview-cont';
                        imgElement.width = 100;
                        imgElement.height = 100;
                        imgElement.className = 'image-class'; // Set class name for styling
                        const aElement = document.createElement('a');
                        aElement.innerHTML ='Remove';
                        aElement.href ='javascript:;';
                        aElement.addEventListener('click',function(e){
                            e.preventDefault();

                            fileBuffer.items.remove(i);
                            fileInput.files = fileBuffer.files;
                            $(this).parent().remove();
                        });
                        prevParent.append(imgElement)
                        prevParent.append(aElement)
                        prevContainer.append(prevParent);

                    }

                    let removedInputs = $(component).find('.removed_files');

                    let serverImages = $(component).find('.server-images a').on('click',function(){
                                let id = $(this).data('id');

                                $(this).parent().remove();
                                let last =$(removedInputs).val();
                                if(last){
                                    last=  last+','+id;
                                }
                                else{
                                    last=  id;
                                }
                                $(removedInputs).val(last);

                    });

            }
                    </script>
";

    }



    function upload($fileName,$path="")
    {
        $path = $fileName->store("public/".$path);
        return  str_replace("public/","storage/",$path);
        // return  AbnCms::upload($fileName,$path==""?$this->getConfig("path","cms"):"cms");

    }
    function uploadwithresize($file,$path,$height=null,$width=null)
    {
            if(!$height || $width)
            {
                $width = 200;
                $height = 200;
            }
            //$width = 200;
            //$height = 200;

            $canvas = Image::canvas($width, $height);
            $fileName = time().rand(111111111,9999999999).'.'.$file->getClientOriginalExtension();
            $destinationPath    = 'storage/uploads/'.$path.'/';
            // Check to see if directory already exists
            $exist = is_dir($destinationPath);
            // If directory doesn't exist, create directory
            if(!$exist) {

                mkdir("$destinationPath");
                chmod("$destinationPath", 0755);
            }
            // upload new image
            $img = Image::make($file->getRealPath())
            // original
            ->save($destinationPath.$fileName)
            // thumbnail
            ->resize($width, $height,function ($constraint) {     $constraint->aspectRatio(); });

            $destinationPathNew = $destinationPath.'thumb/';
            // Check to see if directory already exists
            $existNew = is_dir($destinationPathNew);
            // If directory doesn't exist, create directory
            if(!$existNew) {
                mkdir("$destinationPathNew");
                chmod("$destinationPathNew", 0777);
            }

            $canvas->insert($img, 'center');

            // pass the full path. Canvas overwrites initial image with a logo
            $canvas->save($destinationPath.'thumb/'.$fileName);
            return $destinationPath.$fileName;
    }

    function jsConfig()
    {
        return ["isImage"=>$this->getConfig("isImage",false)];
    }
    function uploadFiles($file){
        return $this->getConfig("isImage",false) && $this->getConfig("resize",false) ? $this->uploadwithresize($file,$this->getConfig("path","cms"),
                $this->getConfig("height",100),
                $this->getConfig("width",100)): $this->upload($file,$this->getConfig("path","files"));
    }
    function onSaveModel($model){
        $attrs = $this->getConfig("attr",[]);
        if(isset($attrs['multiple']) && $attrs['multiple']){

            if(is_array($this->getValue())){
                $images = [];
                foreach($this->getValue() as $file){

                    if($file!="" && $file instanceof \Illuminate\Http\UploadedFile){
                        \Log::info("Uploading....");
                        $images[] =$this->uploadFiles($file);

                    }
                }
                $model->{$this->getConfig("name")} = json_encode($images);
            }
        }
        else{
            if($this->getValue()!="" && $this->getValue() instanceof \Illuminate\Http\UploadedFile){

                \Log::info("Uploading....");
                $model->{$this->getConfig("name")} =$this->uploadFiles($this->getValue());

            }
        }

            return $model;
    }

    function buildInput($name,$attrs){

        $files = $this->getValue();

        $prv="";
        $isImage = $this->getConfig("isImage",false);

        if(isset($attrs['multiple']) && $attrs['multiple']){
            if(isset($attrs["name"])){
                $attrs["name"]=$attrs["name"]."[]";
            }
            else
            $name=$name."[]";
        }

        if(!empty($files)){
            if(isset($attrs['multiple'])){
                $files = json_decode($files);

                foreach($files as $id=>$file){
                    $attachment = $isImage? "<img src='$file' width=100 height=100 />":"Attachment";
                    $prv.="<div class='server-images'>$attachment<a data-id='$id' href='javascript:;'>Remove</a></div>";
                }
            }
            else{
                $base = $files;
                if($this->getConfig("base_url","")!=''){
                    $base = $this->getConfig("base_url","")."/".$files;
                }


                $attachment = $isImage? "<img src='$base' width=100 height=100 />":"Attachment";
                $prv.="<div class='preview-cont'>$attachment</div>";
            }
        }

        $prv.="<input type='hidden' name='removed_files' class='removed_files' />";
        $image =  '<div class="img-preview-container"></div> ';
        return \Form::file($name, $attrs).$prv.$image;

    }


}
