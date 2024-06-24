<div  x-ref="columns" > 
        <template x-for="(val,key) in columns">
            
            <div class="row"> 
              <div><span x-text="val.column"></span>  </div>
       
               
                <select x-model="val.class">
                     <template x-for="(cmp, cmp_key) in components">
                         <option x-bind:value="cmp.class" x-text="cmp_key"> </option>
                     </template>
                </select>
                <template x-for="(conf, k) in val.config">
                    <div class="col col-3"  >  
                        <input type="text" x-model="val.config[k]"   x-bind:placeholder="conf" />
                    </div>
                    
                </template>
               <button type="button" @click="removeColumn(key)">Remove</button>   
             </div>
            
            
        </template>
        <button type="button" @click="addColumn()">Add Column</button>
        <button type="button" @click="print()">Generate Controller</button>
        <button type="button" @click="print(2)">Generate Model</button>
         <button type="button" @click="save()">Save Controller </button>
           <button type="button" @click="save(2)">Save Model </button>
    </div>