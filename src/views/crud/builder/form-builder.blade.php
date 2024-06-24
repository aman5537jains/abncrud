@extends('front.layouts.dashboard')

@section('content')
    <div class="dbody">
        <div class="dbody-inner">
            @include('message')
            <div class="dashHead">
                
            </div>
            <div class="dashBoard-tiles">
                <div class="dashBoard-tile">
                    <div class="dForm">
                        <div x-data='dropdown'>
                         {!!  $form->render() !!} 

                         <div>
                            <textarea style="width:300px; height:100%;" x-model="code.controller"></textarea>
                             <textarea style="width:300px; height:100%;" x-model="code.model"></textarea>
                         </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
@endsection

@section('uniquepagescript') 
<?php 
    echo \App\Lib\CRUD\CrudService::js();
?>
 
       <script src="//unpkg.com/alpinejs" defer></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.13.0/Sortable.min.js" ></script>
       
       <script>
       document.addEventListener('alpine:init', () => {
        
        Alpine.data("dropdown", () => ( {columns:[],form:{table:""},code:{controller:""},components:{},
        init:function(){
            $.get("{{$url}}/getComponents?table="+this.form.table, (data)=>{
                            this.components=  data;
                            console.log("this.components",this.components)
                        })
        },
        getTable(val){
            let table = this.form.table;
            $.get("{{$url}}/getTableConfig?table="+table, (data)=>{
                if(data){
                        this.columns= data.columns
                        this.form= data;
                        this.form.table=table;
                        for(m in data)
                            $('#'+m).val(data[m]);
                        Sortable.create(this.$refs.columns, {
                                    animation: 150,
                                    ghostClass: 'opacity-20',
                                    dragClass: 'bg-blue-50',
                                    onEnd: (event) => {
                                
                                // V3 helper to unwrap the proxy
                                const columns = Alpine.raw(this.columns)
                                
                                // That way we know there are no side effects
                                const droppedAtItem = columns.splice(event.oldIndex, 1)[0]
                                columns.splice(event.newIndex, 0, droppedAtItem)
                                
                                
                                this.columns = columns
                                }
                        });
                    }
                    })
            
        },
        addColumn(){
           let name= prompt("Please enter column name");
            this.columns.push( {column:name,class:"App\Lib\CRUD\Components\InputComponent",config:{type:"text"}})
        } ,
        removeColumn(name){
        //    let name= prompt("Please enter column name");

              this.columns.splice(name, 1);
        }    ,
          print(type=1){
            console.log(" this.columns", this.columns)
            if(type==1){
                const form = this.form;
                form._token="{{ csrf_token() }}";
                $.post("{{$url}}/generateController",form, (data)=>{
                            this.code.controller=data
                        })
            }
            if(type==2){
                const form = this.form;
                form._token="{{ csrf_token() }}";
                $.post("{{$url}}/generateModel",form, (data)=>{
                            this.code.model=data
                })
            }
            
        } ,
           save(type=1){
            console.log(" this.columns", this.columns)
            if(type==1){
                const form = this.form;
                form._token="{{ csrf_token() }}";
                form.code = this.code;
                $.post("{{$url}}/saveController",form, (data)=>{
                           
                        })
            }
            if(type==2){
                const form = this.form;
                form._token="{{ csrf_token() }}";
                form.code = this.code;
                $.post("{{$url}}/saveModel",form, (data)=>{
                           
                        })
            }
            
        }    
        }))
        })
       </script>

@endsection