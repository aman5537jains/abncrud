<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts;

use Aman5537jains\AbnCmsCRUD\Layout;

class BoxLayout  extends Layout
{
  function view(){
        $rows = $this->getValue();
        $total = $rows->total();
        $mapper=  $this->getConfig("mapper",[
          "title"=>"title",
          "description"=>"description",
          "date"=>"log_date",
          "sub_title"=>"sub_title"
        ]);
        // dd($this->getResults());
        return view("crud.box",["rows"=>$this->getResults(),
                "fields"=>$this->getFields(),
                'total'=>$total,
                'mapper'=>$mapper,
                "component"=>$this,
                'pagination'=>$rows->appends($_GET)->links()
      ]);

    }
}
