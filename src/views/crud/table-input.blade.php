 <table id="closed_orders_datatables dsd" class='table'>
         <thead>
          <tr class="list-row">
             @foreach($fields as $field)
             <th> {{ $field->getLabel() }}</th>
             @endforeach
          </tr>
         </thead>
         <tbody>
          @foreach($rows as $key=>$row)
             <tr>
                   @foreach($row as $col)
                      <td> {!! $col->render() !!} </td>
                   @endforeach
             </tr>
          @endforeach
        </tbody>
</table>
