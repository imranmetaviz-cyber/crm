
@extends('layout.master')
@section('title', 'Edit GRN')
@section('header-css')
<style type="text/css">


#selectedItems td input{
  border: none;
}
 
</style>
<link href="{{asset('public/own/inputpicker/jquery.inputpicker.css')}}" rel="stylesheet" type="text/css">
@endsection
@section('content-header')


       <div class="row default-header"  >
          <div class="col-sm-6">
            <h1>Goods Receipt Note</h1>
           </div>
          <div class="col-sm-6 text-right">

             <button form="purchase_order_form" type="submit" class="btn btn-primary"><span class="fas fa-save">&nbsp</span>Update</button>
             
             <button type="button" data-toggle="modal" class="btn btn-danger"  data-target="#modal-del">
                  <span class="fas fa-trash"></span>Delete
                </button>

            <a class="btn btn-transparent" href="{{url('/purchase/goods-receiving-note')}}" ><span class="fas fa-plus">&nbsp</span>New</a>
            <a class="btn btn-transparent" href="{{url('purchase/grn/history')}}" ><span class="fas fa-history">&nbsp</span>History</a>

              <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
                      <i class="fa fa-print"></i>&nbsp;Print<i class="caret"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a href="{{url('/grn/inward-gatepass/'.$grn['grn_no'])}}" class="dropdown-item">Inward Gatepass</a></li>
                    </ul>
                  </div>

            
          </div>
        </div>

           <ol class="breadcrumb default-breadcrumb"  >
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Purchase</a></li>
              <li class="breadcrumb-item"><a href="#">Goods Receipt Note</a></li>
              <li class="breadcrumb-item active">Edit</li>
            </ol>

  @endsection

@section('content')
    <!-- Main content -->

       <!-- /.delete modal -->
          <div class="modal fade" id="modal-del">
        <div class="modal-dialog">
          <div class="modal-content bg-info">
            <div class="modal-header">
              <h4 class="modal-title ">Confirmation</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Do you want to delete?&hellip;</p>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-outline-light" data-dismiss="modal">No</button>
              <button form="delete_form" class="btn btn-outline-light">Yes</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.delete modal -->

<form role="form" id="delete_form" method="POST" action="{{url('purchase/grn/delete/'.$grn['id'])}}">
               
               @csrf    
    </form>

    <!-- Content Header (Page header) -->
    <form role="form" id="purchase_order_form" method="POST" action="{{url('update/purchase/grn')}}">
      <input type="hidden" value="{{csrf_token()}}" name="_token"/>
      <input type="hidden" value="{{$grn['id']}}" name="id"/>
    
      <div class="container-fluid" style="margin-top: 10px;">

            @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('success') }}
    </div>
             @endif

             @if(session()->has('info'))
    <div class="alert alert-info alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('info') }}
    </div>
             @endif

             @if ($errors->has('error'))                       
                      <div class="alert alert-danger alert-dismissible alert-inline">
                                    <button type="button" class="close" data-dismiss="alert" style="">&times;</button>
                                       {{ $errors->first('error') }}
                                          </div>  
                                @endif

             
                      
     
   <div id="std_error" style="display: none;"><p class="text-danger" id="std_error_txt"></p></div>
            <div class="row">
              <div class="col-md-3">
                  
                 <div class="form-section">
                   <h4 class="form-section-title"><i class="fas fa-file-alt mr-2"></i>Document</h4>

                <!-- /.form-group -->
                <div class="form-group">
                  <label>GRN No.</label>
                  <input type="text" name="grn_no" class="form-control " value="{{$grn['grn_no']}}" required readonly style="width: 100%;">
                  </div>

                <div class="form-group">
                  <label>Date</label>
                  <input type="date" name="doc_date" class="form-control " value="{{$grn['doc_date']}}" required style="width: 100%;">
                  </div>

                

                <div class="form-group">
                  <input type="checkbox" name="posted" value="post" id="posted" class=""  >
                  <label>Posted</label>
                  </div>



              </div>

                <!-- <fieldset class="border p-4">
                   <legend class="w-auto"></legend>

                   <div class="form-group">
                  <input type="radio" name="" value=""  class=""  checked>
                  <label>All</label>
                    <input type="radio" name="" value=""  class=""  >
                  <label>Vendor</label>
                  </div>

                  
                    </fieldset> -->
                

               

               
                  

               
                
              </div>
              <!-- /.col -->
              <div class="col-md-4">

               
                    <div class="form-section">
                   <h4 class="form-section-title"><i class="fas fa-user mr-2"></i>Vendor</h4>


                   <div class="form-group">
                  <label class="">Vendor Name</label>
                  <select class="form-control select2" name="vendor_id" id="vendor_id" onchange="setPos()">
                    <option value="">Select any vendor</option>
                    @foreach($vendors as $vendor)
                    <?php
                         $s='';
                         $id=$grn['vendor_id'];
                            if($id==$vendor['id'])
                              $s='selected';
                     ?>
                    <option value="{{$vendor['id']}}" {{$s}}>{{$vendor['name']}}</option>
                    @endforeach
                   
                  </select>
                  </div>

    

                    

                  <!-- <div class="form-group">
                  <label>LC</label>
                   <select class="form-control" name="lc" id="lc" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    <option value="">No</option>
                    <option value="">No</option>
                  </select>
                  </div>

                  <div class="form-group">
                  <label>DC No.</label>
                  <input type="text" name="dc_no" class="form-control " value=""  style="width: 100%;">
                  </div>

                <div class="form-group">
                  <label>Dc Date</label>
                  <input type="date" name="dc_date" class="form-control " value=""  style="width: 100%;">
                  </div> -->

                    <div class="form-group">
                  <label>PO</label>
                  <div class="row">
                    <div class="col-md-9">
                   <select class="form-control select2" name="po_id" id="po_id" >
                    <option value="">Select any PO</option>
                    @foreach($orders as $order)
                    <option value="{{$order['id']}}">{{$order['doc_no'].'~~ '.$order['doc_date']}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <button style="" form="load_po_items" class="btn btn-secondary"  onclick="loadPO()">Load</button>
              </div>
                  </div>

                  <!-- <div class="form-group">
                  <label>Project</label>
                   <select class="form-control" name="project" id="project" style="width: 100%;">
                    <option value="">Select any value</option>
                    <option value="">1</option>
                    <option value="">2</option>
                  </select>
                  </div> -->

                   <div class="form-group">
                  <label>Remarks</label>
                  <textarea name="remarks" class="form-control" >
                    {{$grn['remarks']}}
                  </textarea>
                </div>


                     
                        </div>

                      
             

              </div>
              <!-- /.col -->

              <!-- <div class="col-md-3">

                 <fieldset class="border p-4">
                   <legend class="w-auto"></legend>

                    <div class="form-group">
                  <label>Invoice No</label>
                  <input type="text" name="invoice_no" class="form-control" value="" style="width: 100%;">
                  </div>

                <div class="form-group">
                  <label>Vehicle No</label>
                  <input type="text" name="vehicle" class="form-control " value="" style="width: 100%;">
                  </div>

                   <div class="form-group">
                  <label>Driver Name</label>
                  <input type="text" name="driver" class="form-control" value="" style="width: 100%;">
                  </div>
                   

                    <div class="form-group">
                  <label>IGP No.</label>
                  <input type="text" name="igp" class="form-control" value="" style="width: 100%;">
                  </div>



                   <div class="form-group">
                  <label>Transporter</label>
                   <select class="form-control" name="transporter" id="transporter" style="width: 100%;">
                    <option value="">Select any value</option>
                    <option value="">1</option>
                    <option value="">2</option>
                  </select>
                  </div>

                 
                    </fieldset>

                        <fieldset class="border p-4 mt-5">
                          <legend class="w-auto">Dispatch Time</legend>
                          <div class="form-group">
                  <label>In Time</label>
                  <input type="time" name="in_time" class="form-control" value="" style="width: 100%;">
                  </div>

                  <div class="form-group">
                  <label>Out Time</label>
                  <input type="time" name="out_time" class="form-control" value="" style="width: 100%;">
                  </div>
                         </fieldset>

                         

            
              </div> -->
              <!-- /.col -->

            </div>
            <!-- /.row -->

           </div>
               
                
                <div class="form-section">
                   <h4 class="form-section-title"><i class="fas fa-plus-circle mr-2"></i>Add Item</h4>

              
    
   <div id="item_add_error" style="display: none;"><p class="text-danger" id="item_add_error_txt"></p></div>
      
                   
                   <div class="row">

                    <div class="col-md-2">
                    <div class="form-group">
                  <label>Department</label>
                  <select class="form-control" onchange="setDepartmentItem()" form="add_item" name="location" id="location" style="width: 100%;">
                    <option value="">---Select any value---</option>
                    @foreach($departments as $depart)
                    <option value="{{$depart['id']}}">{{$depart['name']}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

                 <div class="col-md-3">
                 <div class="dropdown" id="items_table_item_dropdown">
        <label>Item</label>
        <input class="form-control"  name="" id="item_code">
      
    
 
  
</div>
</div>

                 <div class="col-md-1">
                    <div class="form-group">
                  <label>Stock</label>
                  <input type="text" form="add_item" name="stock" id="stock" onchange="" class="form-control" readonly style="width: 100%;">
                </div>
              </div>


                 <div class="col-md-1">
                    <div class="form-group">
                  <label>Unit</label>
                  <select class="form-control" form="add_item" name="unit" id="unit" required="true" onchange="setPackSize()" style="width: 100%;">
                    <!-- <option value="">Select Unit</option> -->
                    <option value="loose" selected>Loose</option>
                    <option value="pack">Pack</option>
                  </select>
                </div>
              </div>

              <div class="col-md-1">
                    <div class="form-group">
                  <label>Qty</label>
                  <input type="number" min="1" value="1" form="add_item" name="qty" id="qty" onchange="setRecAndTotalQty()" class="form-control" required="true" style="width: 100%;">
                </div>
              </div>

              <div class="col-md-1">
                    <div class="form-group">
                  <label>Rec Qty</label>
                  <input type="number" min="0" value="1" form="add_item" name="rec_qty" id="rec_qty" onchange="setGross()" class="form-control" style="width: 100%;">
                </div>
              </div>


              <div class="col-md-1">
                    <div class="form-group">
                  <label>Pack Size</label>
                  <input type="number" form="add_item" name="p_s" id="p_s" value="1"  onchange="setTotalQty()" class="form-control" readonly required="true" style="width: 100%;">
                </div>
              </div>


              <div class="col-md-2">
                    <div class="form-group">
                  <label>Batch No</label>
                  <input type="text" form="add_item" name="batch_no" id="batch_no"  class="form-control"  style="width: 100%;">
                </div>
              </div>

              <div class="col-md-2">
                    <div class="form-group">
                  <label>Mfg Date</label>
                  <input type="date" form="add_item" name="mfg_date" id="mfg_date"  class="form-control"  style="width: 100%;">
                </div>
              </div>

              <div class="col-md-2">
                    <div class="form-group">
                  <label>Exp Date</label>
                  <input type="date" form="add_item" name="exp_date" id="exp_date"  class="form-control"  style="width: 100%;">
                </div>
              </div>

              

              
               <div class="col-md-1">
                    <div class="form-group">
                  <label>Rej Qty</label>
                  <input type="number" min="0" value="0" form="add_item" name="rej_qty" id="rej_qty" onchange="setGross()" class="form-control" style="width: 100%;">
                </div>
              </div>

              <div class="col-md-1">
                    <div class="form-group">
                  <label>Gross Qty</label>
                  <input type="number" min="1" value="1" form="add_item" name="gross_qty" id="gross_qty" onchange="setTotalQty()" class="form-control" style="width: 100%;">
                </div>
              </div>

              <div class="col-md-2">
                    <div class="form-group">
                  <label>Total Qty</label>
                  <input type="number" form="add_item" name="total_qty" id="total_qty" min="1" value="1"  class="form-control" required="true" readonly style="width: 100%;">
                </div>
              </div>

              

               <div class="col-md-1">
                    <div class="form-group">
                  <br>
                  <label><input type="checkbox" form="add_item" value="1" name="is_sampled" id="is_sampled">&nbspSampled</label>
                </div>
              </div>
               

              

              <div class="col-md-1">
                    <div class="form-group">
                      <label style="visibility: hidden;">Net</label>
                  <input type="hidden" name="row_id" id="row_id" >
                    
                  <button type="button" form="add_item" class="btn" id="add_item_btn" style="width: 100%;" onclick="addItem()">
                  <span class="fa fa-plus-circle text-info"></span>
                  </button>
                </div>
              </div>


</div> <!--end row-->



                 </div>



          



<!-- Start Tabs -->
<div class="form-section mb-4 p-3">
<div class="nav-tabs-wrapper mb-3">
    <ul class="nav nav-tabs dragscroll horizontal">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabA"><i class="fas fa-list mr-2"></i>Items</a></li>
        
        
    </ul>
</div>

<span class="nav-tabs-wrapper-border" role="presentation"></span>

<div class="tab-content" style="">
    <div class="tab-pane fade show active" id="tabA">
      
     

     
                 <div class="table-responsive">
                 <table class="table table-bordered table-hover" id="item_table">
        <thead class="table-primary">
           <tr>
              <th>No.</th>
             <th>Location</th>
             <th>Code</th>
             <th>Item</th>
            
             <th>UOM</th>
             <th>Unit</th>
             <th>Qty</th>
             <th>Rec Qty</th>
             
              <th>Pack Size</th>
               <th>Batch No</th>
              <th>Mfg Date</th>
              <th>Exp Date</th>
             
             <th>Rej Qty</th>
             <th>Gross Qty</th>
             <th>Total Qty</th>
             <th>Is Sampled</th>
             <th></th>
           </tr>
        </thead>
        <tbody id="selectedItems">

           <?php $rows=1; ?>
          @foreach($grn['items'] as $item)
        <tr ondblclick="(editItem('{{$rows}}'))" id="{{$rows}}">
          <td><input type="hidden"  id="{{'pivot_id_'.$rows}}" name="pivot_ids[]" form="purchase_order_form" value="{{$item['pivot_id']}}"  ></td>
          <td><input type="text"  id="{{'location_'.$rows}}" name="locations[]" form="purchase_order_form" value="{{$item['location_name']}}" readonly ><input type="hidden"  id="{{'location_id_'.$rows}}" name="location_ids[]" form="purchase_order_form" value="{{$item['location_id']}}"  ></td>
          <td><input type="text" form="purchase_order_form"  id="{{'code_'.$rows}}" name="codes[]" value="{{$item['code']}}" readonly ></td>
          <td><input type="text" form="purchase_order_form"  id="{{'item_name_'.$rows}}" name="items_name[]" form="purchase_order_form" value="{{$item['name']}}" readonly ><input type="hidden" form="purchase_order_form"  id="{{'item_id_'.$rows}}" name="items_id[]" value="{{$item['id']}}" readonly ></td>
          
          <td><input type="text" form="purchase_order_form" id="{{'uom_'.$rows}}" name="" value="{{$item['uom']}}" readonly ></td>
          <td><input type="text" form="purchase_order_form"  id="{{'unit_'.$rows}}" name="units[]" value="{{$item['unit']}}" readonly ></td>
          <td><input type="text"  id="{{'qty_'.$rows}}" form="purchase_order_form" name="qtys[]" value="{{$item['quantity']}}" readonly ></td>
          <td><input type="text"  id="{{'rec_qty_'.$rows}}" form="purchase_order_form" name="rec_qty[]" value="{{$item['rec_qty']}}" readonly ></td>
          <td><input type="text"  id="{{'p_s_'.$rows}}" form="purchase_order_form" name="p_s[]" value="{{$item['pack_size']}}" readonly ></td>
          
          <td><input type="text"  id="{{'batch_no_'.$rows}}" form="purchase_order_form" name="batch_no[]" value="{{$item['batch_no']}}" readonly ></td>
          <td><input type="date"  id="{{'mfg_date_'.$rows}}" form="purchase_order_form" name="mfg_date[]" value="{{$item['mfg_date']}}" readonly ></td>
          <td><input type="date"  id="{{'exp_date_'.$rows}}" form="purchase_order_form" name="exp_date[]" value="{{$item['exp_date']}}" readonly ></td>


          <td><input type="text"  id="{{'rej_qty_'.$rows}}" form="purchase_order_form" name="rej_qty[]" value="{{$item['rej_qty']}}" readonly ></td>
           <td><input type="text"  id="{{'gross_qty_'.$rows}}" form="purchase_order_form" name="gross_qty[]" value="{{$item['gross_qty']}}" readonly ></td>
          <td><input type="text"  id="{{'total_qty_'.$rows}}" form="purchase_order_form" name="total_qty[]" value="{{$item['total_qty']}}" readonly ></td>
           
           <?php
               $is_sampled=0; $checked='';
              
               if($item['is_sampled']==1)
               { $is_sampled=1; $checked='checked';}
            ?>
          <td><input type="checkbox" form="purchase_order_form" name="is_sampled[]" value="1" id="{{'is_sampled_'.$rows}}" {{$checked}} ><input type="hidden" form="purchase_order_form" name="sampled[]" id="{{'sampled_'.$rows}}" value="{{$is_sampled}}"></td>
           

         <td><button type="button" class="btn" onclick="removeItem('{{$rows}}')"><span class="fa fa-minus-circle text-danger"></span></button></td>
     </tr>
     <?php $rows++; ?>
     @endforeach

          
        </tbody>
        <tfoot class="table-secondary">
          <tr>
            <th>Qty</th>
            <td id="net_qty">{{$grn['total_qty']}}</td>
            <th>Rec Qty</th>
            <td id="net_rec_qty">{{$grn['total_rec_qty']}}</td>
            <th>Rej Qty</th>
            <td id="net_rej_qty">{{$grn['total_rej_qty']}}</td>
            <th>Gross Qty</th>
            <td id="net_gross_qty">{{$grn['total_gross_qty']}}</td>
            <th>Net Qty</th>
            <td id="total_net_qty">{{$grn['total_net_qty']}}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
     </div>
        
    </div>
    

  

    

    
</div>
</div>
<!-- End Tabs -->
                   



        
      </div>

      </form>
    <!-- /.content -->

      <form role="form" id="add_item">
              
            </form>

            <form role="form" id="#load_po_items">
              
            </form>
   
@endsection

@section('jquery-code')
<script src="{{asset('public/own/inputpicker/jquery.inputpicker.js')}}"></script>
<script type="text/javascript">

var row_num=<?php echo json_encode( $rows ) ; ?>; 

function getRowNum()
 {
  return this.row_num;
}

function setRowNum()
 {
   this.row_num++;
}

$(document).ready(function(){

   $('.select2').select2(); 

  $('#purchase_order_form').submit(function(e) {

    e.preventDefault();
//alert(this.row_num);
var row_num=getRowNum();

for (var i =1; i <= row_num ;  i++)
{
if ($(`#total_qty_${i}`). length > 0 )
     {
        $('#std_error_txt').html('');
               this.submit();
               return ;
      }
  }

             
               $('#std_error').show();
               $('#std_error_txt').html('Select items!');
             
             
  });


   
});

 function setInventory(items)
 {
  
  
  var new_items=[];
 
 for(var i = 0 ; i < items.length ; i++)
 {
  var item_color='',item_type='',item_unit='',item_size='',item_category='';
  if(items[i]['color']!=null)
    item_color=items[i]['color']['name'];
   if(items[i]['type']!=null)
    item_type=items[i]['type']['name'];
   if(items[i]['unit']!=null)
    item_unit=items[i]['unit']['name'];
   if(items[i]['size']!=null)
    item_size=items[i]['size']['name'];
   if(items[i]['category']!=null)
    item_category=items[i]['category']['name'];

         var let={code:items[i]['item_code'],item:items[i]['item_name'],type:item_type,category:item_category,size:item_size,color:item_color,unit:item_unit};
         //alert(let);
         new_items.push(let);
 }


$('#item_code').inputpicker({
    data:new_items,
    fields:[
        {name:'code',text:'Code'},
        {name:'item',text:'Item'},
        {name:'type',text:'Type'},
        {name:'category',text:'Category'},
        {name:'size',text:'Size'},
        {name:'color',text:'Color'},
        {name:'unit',text:'Unit'}
    ],
    headShow: true,
    fieldText : 'item',
    fieldValue: 'code',
  filterOpen: true
    });

 }

$(document).ready(function(){

  
  $('#po_id').val('{{$grn['purchaseorder_id']}}');
  $('#po_id').trigger('change');

  
  
  value="{{$grn['posted'] }}";
   
   
   if(value=="post")
   {
    
  $('#posted').prop("checked", true);
   
   }
    else{
      $('#posted').prop("checked", false);
  
    } 

  

  var items=[] ;

 setInventory(items);
  
  

});

 
 function setDepartmentItem()
{
   $("#item_name").val('');
    $("#item_id").val('');
    $("#item_code").val('');
    $("#item_size").val('');
    $("#item_color").val('');
   $("#item_unit").val('');

    $.ajax({
               type:'get',
               url:'{{ url("/department/inventory") }}',
               data:{
                    
                    // "_token": "{{ csrf_token() }}",
                    
                     department_id: jQuery('#location').val(),
                  

               },
               success:function(data) {

                items=data;
                 
                 
                 setInventory(items);



               }
             });
    
}//end setDepartmentItem



function select_vendor(name,id)
{
  //var name=this.name;
  //alert(name); alert(id);
    $("#vendor_name").val(name);
    $("#vendor_id").val(id);
}




function setPackSize()
{
  unit=$("#unit").val();
  if(unit=='' || unit=='loose')
  {
     document.getElementById('p_s').setAttribute('readonly', 'readonly');
     $("#p_s").val('1');
      
        setTotalQty();


   }
   else if( unit=='pack')
   {
    document.getElementById('p_s').removeAttribute('readonly');
    
    }
}

function setTotalQty()
{
  qty=$("#qty").val();
  p_s=$("#p_s").val();
  rec_qty=$("#rec_qty").val();
  rej_qty=$("#rej_qty").val();
   gross=$("#gross_qty").val();

  if(rej_qty=='')
    rej_qty=0;

  total_qty=(gross * p_s);

  // if(p_s!='' )
  //    total_qty=total_qty*p_s;
   
   $("#total_qty").val(total_qty); 

   

}

function setRecAndTotalQty()
{
  

  qty=$("#qty").val();

  $("#rec_qty").val(qty);

     setGross();



}

function setGross()
{
   var rec_qty=$("#rec_qty").val();
   var rej_qty=$("#rej_qty").val();
   var gross= rec_qty -  rej_qty;
  
  if(rej_qty == '')
    rej_qty=0;

 $("#gross_qty").val(gross);

 setTotalQty();

}



function checkItem(row='')
{
  var rows=getRowNum();
  for (var i =1; i <= rows ;  i++) {
   
     if ($(`#item_id_${i}`). length == 0 || $(`#item_id_${i}`). length < 0 )
     {
      continue;
     }
     if (row == i  )
     {
      continue;
     }
     var item_combine=$("#item_code").val();

    item_combine=item_combine.split('_');
    var item_id='';
    if(item_combine!='')
    {     item_id=item_combine[11];
     }

     var tbl_item_id=$(`#item_id_${i}`).val(); 

     if(item_id == tbl_item_id)
      return true;
     
  
   }
  return false;   
   
}

function updateItem(row)
{
  
 var item_code=$("#item_code").val();

  var location=$("#location").val();
   var location_text=$("#location option:selected").text();
   if(location=='')
    location_text='';

  var unit=$("#unit").val();
  var qty=$("#qty").val();

  var p_s=$("#p_s").val();
  var total_qty=$("#total_qty").val();

  var batch_no=$("#batch_no").val();
  var mfg_date=$("#mfg_date").val();
  var exp_date=$("#exp_date").val();

  var rec_qty=$("#rec_qty").val();
  var rej_qty=$("#rej_qty").val();
var gross=$("#gross_qty").val();
  var is_sampled=$("#is_sampled").prop("checked");

     
     if(item_code==''  || unit=='' || rec_qty=='')
     {
        var err_name='',err_unit='',err_qty='',err_rec_qty='';
           
           if(item_code=='')
           {
                err_name='Item is required.';
           }
           
           if(unit=='')
           {
            err_unit='Unit  is required.';
           }
           // if(qty=='')
           // {
           //  err_qty='Quantity is required.';
           // }

           if(rec_qty=='')
           {
            err_rec_qty='Rec Quantity is required.';
           }



           $("#item_add_error").show();
           $("#item_add_error_txt").html(err_name+'  '+err_unit+' '+err_qty+' '+err_rec_qty);

     }
     else
     {

       $(`#code_${row}`).val(item_code);

          $(`#is_sampled_${row}`).prop("checked", is_sampled );

      if(is_sampled==true)
          $(`#sampled_${row}`).val("1" );
       else
        $(`#sampled_${row}`).val("0" );


                      $(`#location_id_${row}`).val(location);
                      $('#location').val(location_text);

                    $(`#unit_${row}`).val(unit);

                         $(`#qty_${row}`).val(qty);



                           $(`#p_s_${row}`).val(p_s);

                           $(`#batch_no_${row}`).val(batch_no);
                           $(`#mfg_date_${row}`).val(mfg_date);
                           $(`#exp_date_${row}`).val(exp_date);
 
                         $(`#total_qty_${row}`).val(total_qty);
  
                             $(`#rec_qty_${row}`).val(rec_qty);
  
                        $(`#rej_qty_${row}`).val(rej_qty);
 
                       $(`#gross_qty_${row}`).val(gross);
  

     $.ajax({
               type:'get',
               url:'{{ url("/inventory/item/") }}',
               data:{
                    
                    
                    
                     item_code : jQuery('#item_code').val(),
                      

               },
               success:function(data) {
                name='#item_name_'+row;
              
                
                uom='#uom_'+row;
               
                id='#item_id_'+row;
                size_value='',uom_value='',color_value='';
                
                if(data['unit']!=null)
                  uom_value=data['unit']['name'];
 //var name=$(id).val(); alert(name);
                   // alert(JSON.stringify(data));
                    $(name).val(data['item_name']);
                   
                    $(id).val(data['id']);
                  
                    $(uom).val(uom_value);
               }

             });

  
 $("#item_code").val('');

   var s=$("#items_table_item_dropdown").find(".inputpicker-input");
   s.val('');
  $("#item_name").val('');
  $("#item_id").val('');
  $("#location").val('');
  
  
  $("#item_unit").val('');
  $("#item_code").val('');
  $("#unit").val('loose');
  $("#qty").val('1');

  $("#p_s").val('1');

  $("#batch_no").val('');
  $("#mfg_date").val('');
  $("#exp_date").val('');

  $("#total_qty").val('1');
  $("#rec_qty").val('1');
  $("#rej_qty").val('0');
  $("#gross_qty").val('1');
  
  $('#row_id').val('');

  $("#item_add_error").hide();
           $("#item_add_error_txt").html('');

            $('#add_item_btn').attr('onclick', `addItem()`);
       setNetQty();
   }

  }// update item

function addItem()
{
  
  var item_code=$("#item_code").val();
  
  
  var location=$("#location").val();
   var location_text=$("#location option:selected").text();
   if(location=='')
    location_text='';

  var unit=$("#unit").val();
  var qty=$("#qty").val();
  var rec_qty=$("#rec_qty").val();
  var rej_qty=$("#rej_qty").val();
  var gross=$("#gross_qty").val();
  var p_s=$("#p_s").val();
  var total_qty=$("#total_qty").val();
  
   var batch_no=$("#batch_no").val();
  var mfg_date=$("#mfg_date").val();
  var exp_date=$("#exp_date").val();
  var is_sampled=$("#is_sampled").prop("checked");
     
     if(item_code==''  || unit=='' ||  rec_qty=='' )
     {
        var err_name='',err_unit='',err_qty='' , err_rec_qty='';
           
           if(item_code=='')
           {
                err_name='Item is required.';
           }
           
           if(unit=='')
           {
            err_unit='Unit  is required.';
           }
           // if(qty=='')
           // {
           //  err_qty='Quantity is required.';
           // }

           if(rec_qty=='')
           {
            err_rec_qty='Rec Quantity is required.';
           }



           $("#item_add_error").show();
           $("#item_add_error_txt").html(err_name+'  '+err_unit+' '+err_qty+' '+err_rec_qty);

     }
     else
     {


      var rows = getRowNum(); 
            
     var txt=`<tr ondblclick="(editItem(${rows}))" id="${rows}"><td><input type="hidden"  id="pivot_id_${rows}" name="pivot_ids[]" form="purchase_order_form" value="0"  ></td><td><input type="text"  id="location_${rows}" name="locations[]" form="purchase_order_form" value="${location_text}" readonly ><input type="hidden"  id="location_id_${rows}" name="location_ids[]" form="purchase_order_form" value="${location}"  ></td><td><input type="text" form="purchase_order_form"  id="code_${rows}" name="codes[]" value="${item_code}" readonly ></td><td><input type="text" form="purchase_order_form"  id="item_name_${rows}" name="items_name[]" form="purchase_order_form" value="" readonly ><input type="hidden" form="purchase_order_form"  id="item_id_${rows}" name="items_id[]" value="" readonly ></td><td><input type="text" form="purchase_order_form" id="uom_${rows}" name="" value="" readonly ></td>
     <td><input type="text" form="purchase_order_form"  id="unit_${rows}" name="units[]" value="${unit}" readonly ></td><td><input type="text"  id="qty_${rows}" form="purchase_order_form" name="qtys[]" value="${qty}" readonly ></td><td><input type="text" form="purchase_order_form" id="rec_qty_${rows}" name="rec_qty[]" value="${rec_qty}" readonly ></td>
     <td><input type="text"  id="p_s_${rows}" form="purchase_order_form" name="p_s[]" value="${p_s}" readonly ></td>

        <td><input type="text"  id="batch_no_${rows}" form="purchase_order_form" name="batch_no[]" value="${batch_no}" readonly ></td>
     <td><input type="date"  id="mfg_date_${rows}" form="purchase_order_form" name="mfg_date[]" value="${mfg_date}" readonly ></td>
     <td><input type="date"  id="exp_date_${rows}" form="purchase_order_form" name="exp_date[]" value="${exp_date}" readonly ></td>


     <td><input type="text" form="purchase_order_form" id="rej_qty_${rows}" name="rej_qty[]" value="${rej_qty}" readonly ></td>
     <td><input type="text" form="purchase_order_form" id="gross_qty_${rows}" name="gross_qty[]" value="${gross}" readonly ></td>
     <td><input type="text"  id="total_qty_${rows}" form="purchase_order_form" name="total_qty[]" value="${total_qty}" readonly ></td>
       
       <td><input type="checkbox" form="purchase_order_form" name="is_sampled[]" value="1" id="is_sampled_${rows}"><input type="hidden" form="purchase_order_form" name="sampled[]" id="sampled_${rows}" value="0"></td>

         <td><button type="button" class="btn" onclick="removeItem(${rows})"><span class="fa fa-minus-circle text-danger"></span></button></td>
     </tr>`;

   

    $("#selectedItems").append(txt);
   setRowNum();

   $(`#is_sampled_${rows}`).prop("checked", is_sampled );

      if(is_sampled==true)
          $(`#sampled_${rows}`).val("1" );
       else
        $(`#sampled_${rows}`).val("0" );
  
                  
     $.ajax({
               type:'get',
               url:'{{ url("/inventory/item/") }}',
               data:{
                    
                    
                    
                     item_code : jQuery('#item_code').val(),
                      

               },
               success:function(data) {
                name='#item_name_'+rows;
              
                
                uom='#uom_'+rows;
                
                id='#item_id_'+rows;
                size_value='',uom_value='',color_value='';
                
                if(data['unit']!=null)
                  uom_value=data['unit']['name'];
 //var name=$(id).val(); alert(name);
                   // alert(JSON.stringify(data));
                    $(name).val(data['item_name']);
                    
                    $(id).val(data['id']);
                    
                    $(uom).val(uom_value);
               }

             });

   $("#item_code").val('');

   var s=$("#items_table_item_dropdown").find(".inputpicker-input");
   s.val('');

  $("#item_name").val('');
  $("#item_id").val('');
  $("#location").val('');
 
  
  $("#unit").val('loose');
  $("#qty").val('1');

  $("#p_s").val('1');
  $("#batch_no").val('');
  $("#mfg_date").val('');
  $("#exp_date").val('');
  $("#total_qty").val('1');
   
  $("#rec_qty").val('1');
  $("#rej_qty").val('0');
 $("#gross_qty").val('1');
  

  $('#row_id').val('');

  $("#item_add_error").hide();
           $("#item_add_error_txt").html('');

            setNetQty();
   
   }


     
}//add item

function editItem(row)
{
   var item_name=$(`#item_name_${row}`).val();
   var item_code=$(`#code_${row}`).val();

   $('#item_code').val(item_code);
var s=$("#items_table_item_dropdown").find(".inputpicker-input");
   s.val(item_name);


var location_id=$(`#location_id_${row}`).val();
$('#location').val(location_id);

var unit=$(`#unit_${row}`).val();
$('#unit').val(unit);



  var qty=$(`#qty_${row}`).val();
$('#qty').val(qty);


  var p_s=$(`#p_s_${row}`).val();
  $('#p_s').val(p_s);

  var batch_no=$(`#batch_no_${row}`).val();
  $('#batch_no').val(batch_no);
var mfg_date=$(`#mfg_date_${row}`).val();
  $('#mfg_date').val(mfg_date);

var exp_date=$(`#exp_date_${row}`).val();
  $('#exp_date').val(exp_date);
  
  var rec_qty=$(`#rec_qty_${row}`).val();
  $('#rec_qty').val(rec_qty);
  var rej_qty=$(`#rej_qty_${row}`).val();
  $('#rej_qty').val(rej_qty);
  var gross=$(`#gross_qty_${row}`).val();
  $('#gross_qty').val(gross);
  var total_qty=$(`#total_qty_${row}`).val();
  $('#total_qty').val(total_qty);
  
   var is_sampled=$(`#is_sampled_${row}`).prop("checked"  );
  $(`#is_sampled`).prop("checked" , is_sampled );

  $('#row_id').val(row);

  $('#add_item_btn').attr('onclick', `updateItem(${row})`);

}

function removeItem(row)
{
  
  $('#item_table tr').click(function(){
    //$(this).remove();
    $(`#${row}`).remove();
    setNetQty();
  
});

}

function setNetQty()
{
  var rows=getRowNum();
   
   var net_qty=0 , net_rec_qty=0, net_rej_qty=0, net_gross_qty=0, total_net_qty=0;   
   for (var i =1; i <= rows ;  i++) {

    if($(`#qty_${i}`). length == 0 || $(`#qty_${i}`). length < 0)
      continue;
    
       var qty=$(`#qty_${i}`).val();
      var rec_qty=$(`#rec_qty_${i}`).val();
      var rej_qty=$(`#rej_qty_${i}`).val();
      var gross=$(`#gross_qty_${i}`).val();
      var total_qty=$(`#total_qty_${i}`).val();

      

      if(qty=='' || qty==null)
        qty=0;

      if(rec_qty=='' || rec_qty==null)
        net_rec_qty=0;

      if(rej_qty=='' || rej_qty==null)
        rej_qty=0;

      if(gross=='' || gross==null)
        gross=0;
      
      if(total_qty=='' || total_qty==null)
        total_qty=0;

        net_qty +=  Number (qty);
        net_rec_qty +=  Number (rec_qty) ;
        net_rej_qty +=  Number (rej_qty);
        net_gross_qty +=  Number (gross);
        total_net_qty +=  Number(total_qty );

   }
   $(`#net_qty`).text(net_qty);
      $(`#net_rec_qty`).text(net_rec_qty);
        $(`#net_rej_qty`).text(net_rej_qty);
        $(`#net_gross_qty`).text(net_gross_qty);
      $(`#total_net_qty`).text(total_net_qty);
      

}



function loadPO()
{
  var po_id = jQuery('#po_id').val();

  if(po_id!='')
  {

  $.ajax({
               type:'get',
               url:'{{ url("/get/po/") }}',
               data:{
                    
                    
                    
                     po_id : po_id,
                      

               },
               success:function(data) {

                order_id=data['id'];
                vendor_id=data['vendor_id'];
                vendor_name=data['vendor_name'];

              

                //$('#vendor_name').val(vendor_name);
                $('#vendor_id').val(vendor_id);
                $('#vendor_id').trigger('change');
                $('#po_id').val(order_id);
                 var rows = getRowNum();
                 //alert(data['items'].length); 
                 var txt='';
                for (var i =0; i < data['items'].length  ; i++) {
                  var item = data['items'][i];
      
                  var rows = getRowNum(); 
            
      txt +=`<tr ondblclick="(editItem(${rows}))" id="${rows}"><td><input type="hidden"  id="pivot_id_${rows}" name="pivot_ids[]" form="purchase_order_form" value="0"  ></td><td><input type="text" form="purchase_order_form" id="location_${rows}" name="locations[]" value="${item['location']}" readonly ><input type="hidden" form="purchase_order_form" id="location_id_${rows}" name="location_ids[]" value="${item['location_id']}"  ></td><td><input type="text" form="purchase_order_form" id="code_${rows}" name="codes[]" value="${item['item_code']}" readonly ></td><td><input type="text" form="purchase_order_form" id="item_name_${rows}" name="items_name[]" value="${item['item_name']}" readonly ><input type="hidden" form="purchase_order_form" id="item_id_${rows}" name="items_id[]" value="${item['item_id']}" readonly ></td><td><input type="text" form="purchase_order_form" id="uom_${rows}" name="" value="${item['item_uom']}" readonly ></td>
     <td><input type="text" form="purchase_order_form" id="unit_${rows}" name="units[]" value="${item['unit']}" readonly ></td><td><input type="text" form="purchase_order_form" id="qty_${rows}" name="qtys[]" value="${item['qty']}" readonly ></td>
       <td><input type="text" form="purchase_order_form" id="rec_qty_${rows}" name="rec_qty[]" value="${item['rec_qty']}" readonly ></td>
     <td><input type="text" form="purchase_order_form" id="p_s_${rows}" name="p_s[]" value="${item['pack_size']}" readonly ></td>

     <td><input type="text"  id="batch_no_${rows}" form="purchase_order_form" name="batch_no[]" value="" readonly ></td>
     <td><input type="date"  id="mfg_date_${rows}" form="purchase_order_form" name="mfg_date[]" value="" readonly ></td>
     <td><input type="date"  id="exp_date_${rows}" form="purchase_order_form" name="exp_date[]" value="" readonly ></td>


      <td><input type="text" form="purchase_order_form" id="rej_qty_${rows}" name="rej_qty[]" value="${item['rej_qty']}" readonly ></td>

      <td><input type="text" form="purchase_order_form" id="gross_qty_${rows}" name="gross_qty[]" value="${item['gross_qty']}" readonly ></td>

     <td><input type="text" form="purchase_order_form" id="total_qty_${rows}" name="total_qty[]" value="${item['total_qty']}" readonly ></td>

        <td><input type="checkbox" form="purchase_order_form" name="is_sampled[]" value="1" id="is_sampled_${rows}"><input type="hidden" form="purchase_order_form" name="sampled[]" id="sampled_${rows}" value="0"></td>

         <td><button type="button" class="btn" onclick="removeItem(${rows})"><span class="fa fa-minus-circle text-danger"></span></button></td>
     </tr>`;

   
                 setRowNum();
    
                }

                $("#selectedItems").html(txt);

                 setNetQty();
                
               }

             });//end ajax
}
     
}// end load item

function setPos()
{
   var vendor_id=$('#vendor_id').val();
   var orders=`<?php echo json_encode( $orders )?>`;

   orders=JSON.parse(orders);

   $('#po_id').empty();

   $("#po_id").append("<option value='' >Select any PO</option>");

   for (var i =0; i < orders.length ;  i++) {
     
     if(orders[i]['vendor_id'] == vendor_id || vendor_id=='' )
    {
     $("#po_id").append("<option value='"+orders[i]['id']+"' >"+orders[i]['doc_no']+'~~'+orders[i]['doc_date']+"</option>");
     $('#po_id').trigger('change');
     } 
   }
  
}

</script>


@endsection  
  