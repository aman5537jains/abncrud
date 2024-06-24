<?php

use App\Http\Controllers\Front\StaffAssignedShiftController;
use Aman5537jains\AbnCmsCRUD\Components\ImageComponent;
use Aman5537jains\AbnCmsCRUD\Components\InputComponent;
use Aman5537jains\AbnCmsCRUD\Components\TextComponent;
use Aman5537jains\AbnCmsCRUD\Components\LinkComponent;
use Aman5537jains\AbnCmsCRUD\Components\FileInputComponent;
use Aman5537jains\AbnCmsCRUD\Components\SelectComponent;
use Aman5537jains\AbnCmsCRUD\Components\AuthComponent;


$status  = ["class"=>TextComponent::class,"config"=>["beforeRender"=>function($component){$component->setValue($component->getValue()=="0"?"In Active":"Active"); }]];

return [
            "modules"=>[

            ],
            "routes"=>[


            ],
            "view_fields"=>[
                "id"=>[],
                "business_id"=>[],
                "branch_id"=>["class"=>TextComponent::class,"config"=>["label"=>"Branch","beforeRender"=>function($component){   $component->setValue(@\App\Models\Branch::find($component->getValue())->branch_name); }]],
                "slug"=>[],
                "can_modify_staff"=>$status,

                "status"=>$status,
                "image"=>["class"=>ImageComponent::class,"config"=>[]],
                "thumb"=>["class"=>ImageComponent::class,"config"=>["height"=>50,"width"=>"50"]],
                "icon"=>["class"=>ImageComponent::class,"config"=>["height"=>50,"width"=>"50"]],
                "description"=>["class"=>TextComponent::class,"config"=>["beforeRender"=>function($component){   $component->setValue(substr($component->getValue(),0,50)); }]]
            ],
            "form_fields"=>[
                "business_id"=>["class"=>AuthComponent::class,"config"=>["business_id"=>true]],
                "branch_id"=>["class"=>AuthComponent::class,"config"=>["branch_id"=>true]],
                "staff_id"=>["class"=>AuthComponent::class,"config"=>["staff_id"=>true]],
                "icon"=>["class"=>FileInputComponent::class,"config"=>[]],
                "image"=>["class"=>FileInputComponent::class,"config"=>[]],
                "status"=>["class"=>InputComponent::class,"config"=>["type"=>"select","value"=>"1","options"=>["1"=>"Active","0"=>"In Active"]]],
            ],
            "components"=>[
                "Text"=>["class"=>InputComponent::class,"config"=>["type"=>"text","componentName"=>"Text"]],
                "Number"=>["class"=>InputComponent::class,"config"=>["type"=>"number","componentName"=>"Text"]],
                "File"=>["class"=>InputComponent::class,"config"=>["type"=>"file"]],
                "Select"=>["class"=>InputComponent::class,"config"=>["type"=>"select"]],
                "AuthBusiness"=>["class"=>AuthComponent::class,"config"=>["business_id"=>true]],
                "AuthBranch"=>["class"=>AuthComponent::class,"config"=>["branch_id"=>true]],

            ]

    ];
