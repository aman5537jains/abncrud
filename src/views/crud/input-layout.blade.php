<table id="closed_orders_datatables" class='table'>
    <thead>
     <tr class="list-row">
        @foreach($fields as $field)
        <th>{!!$field->getLabel() !!}</th>
        @endforeach
     </tr>
    </thead>
    <tbody>
     @foreach($rows as $key=>$row)
        <tr  >
              @foreach($row as $col)
              <td> {!! $col->render() !!}</td>
              @endforeach
        </tr>
     @endforeach
     @if(count($rows)<=0)
     <tr  >
        <td align="center" colspan="{{count($fields)}}">  No Records</td>

     </tr >
     @endif
   </tbody>
</table>
