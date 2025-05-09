
 @if( $edit_button)<a href='{{route($module.".edit",[$row->$id])}} '>Edit</a> @endif

 @if( $delete_button) <a onclick='return confirmDeleteComponent(this)' href='{{route($module.".delete",[$row->$id])}}'>Delete</a> @endif
 @if( $view_button) <a  href='{{route($module.".show",[$row->$id])}}'>View</a> @endif
 

        