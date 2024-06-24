<?php

namespace Aman5537jains\AbnCmsCRUD\Layouts;

use Aman5537jains\AbnCmsCRUD\Layout;
use Aman5537jains\AbnCmsCRUD\Components\ActionComponent;
use Aman5537jains\AbnCmsCRUD\Components\ChangeStatusComponent;
use Aman5537jains\AbnCmsCRUD\Components\FileInputComponent;
use Aman5537jains\AbnCmsCRUD\Components\ImageComponent;
use Aman5537jains\AbnCmsCRUD\Components\InputComponent;
use Aman5537jains\AbnCmsCRUD\Components\SubmitButtonComponent;
use Aman5537jains\AbnCmsCRUD\Components\TextComponent;
use Aman5537jains\AbnCmsCRUD\Layouts\TableLayout;

class DataTableLayout  extends TableLayout
{


  function js(){
    $csrf=csrf_token();
    return <<<js
    <script>
    $(function() {
      var table = $('#datatable_1').DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
             language: {
              searchPlaceholder: "Search"
          },
          "bDestroy": true,
          "ordering": false,
          order: [[0, "desc" ]],
          createdRow: function( row, data, dataIndex ) {

            $( row ).addClass('list-row');
        },
           "ajax":{
                       "url": $('#datatable_1').data("url") ,
                       "dataType": "json",
                       "data":{ is_post: 1,_token:"$csrf"}
                     },
              });
          });
          </script>
    js;
  }

  public function isPost(){
        return request()->get("is_post",false);
  }


  public function sendJson(){
           $search  = $this->search();
           $limit      = request()->input('length');
           $start      = request()->input('start');
           $count  = $search->count();
           $model =   $search->offset($start)->limit($limit);

           $this->setValue($model->get());
           $this->build();
           $rowOriginal = $this->getValue();
           $rows = $this->getResults();
            $arrs=[];
           foreach($rows  as $key=>$row){
            $arr=[];
            foreach($row as  $fld=>$cls){
                 $arr[]=$cls->render();
            }
            $arrs[]=$arr;

        }


        $request = request();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($count),
            "recordsFiltered" => intval($count),
            "data"            =>   $arrs,
            "sql"=>$search->toSql()
        );

        return response()->json($json_data);
  }


  function view(){

        $fields = $this->getFields();
        $th='';
        foreach($fields as $field){
            $th.= "<th>".$field->getLabel()."</th>";
        }
        return <<<view

        <div class="listing">
            <table  class="table_has_status_noClass dataTable no-footer " id="datatable_1">
            <thead>
            <tr>
            $th
            </tr>
            </thead>
            </table>
        </div>
    view;

    }
}
