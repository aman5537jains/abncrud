<?php 

namespace Aman5537jains\AbnCmsCRUD\Layouts;

use Aman5537jains\AbnCmsCRUD\Layout;

class SingleViewLayout  extends OneRowLayout
{

   
   


    function view(){
        
      
        $rows = $this->getValue();
        
        // dd($this->getResults());
        return view("crud.single",["rows"=>$this->getResults(),"fields"=>$this->getFields()]);

    }
}
