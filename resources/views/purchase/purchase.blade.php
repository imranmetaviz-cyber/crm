
@extends('layout.master')
@section('title', 'Invoice')
@section('header-css')
<style type="text/css">

#selectedItems td input{
  border: none;
}
 
</style>
<link href="{{asset('public/own/inputpicker/jquery.inputpicker.css')}}" rel="stylesheet" type="text/css">
@endsection
@section('content-header')
    <!-- Content Header (Page header) -->
   

     

       <div class="row default-header"  >
          <div class="col-sm-6">
            <h1>Purchase Invoice</h1>
           </div>
          <div class="col-sm-6 text-right">

             <button form="purchase_order_form" type="submit" class="btn btn-primary"><span class="fas fa-save"></span>Save</button>
             
           

            <a class="btn btn-transparent" href="{{url('purchase')}}" ><span class="fas fa-plus"></span>New</a>
            <a class="btn btn-transparent" href="{{url('purchase/history')}}" ><span class="fas fa-history"></span>History</a>


            
          </div>
        </div>

           <ol class="breadcrumb default-breadcrumb"  >
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Purchase</a></li>
              <li class="breadcrumb-item"><a href="#">Invoice</a></li>
              <li class="breadcrumb-item active">Add New Purchase</li>
            </ol>

  @endsection

@section('content')
    <!-- Main content -->

 <form role="form" id="purchase_order_form" method="POST" action="{{url('purchase/save')}}">
      <input type="hidden" value="{{csrf_token()}}" name="_token"/>
    
      <div class="container-fluid" style="margin-top: 10px;">

            @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('success') }}
    </div>
             @endif

             
              @if ($errors->has('error'))                       
                      <div class="alert alert-danger alert-dismissible alert-inline">
                                    <button type="button" class="close" data-dismiss="alert" style="">&times;</button>
                                       {{ $errors->first('error') }}
                                          </div>  
                                @endif
     

            <div class="row">
              <div class="col-md-3">
                  
                  <div class="form-section">
                   <h4 class="form-section-title"><i class="fas fa-file-alt mr-2"></i>Document</h4>

                <!-- /.form-group -->
                <div class="form-group">
                  <label>Purchase No.</label>
                  <input type="text" name="purchase_no" id="purchase_no" class="form-control" value="{{$doc_no}}" required readonly style="width: 100%;">
                  </div>

                <div class="form-group">
                  <label>Date</label>
                  <input type="date" name="doc_date" class="form-control" value="{{date('Y-m-d')}}" required style="width: 100%;">
                  </div>

                <!-- <div class="form-group">
                  <input type="checkbox" name="is_gst" onchange="setIsGst()" value="1" id="is_gst" class=""  checked>
                  <label>GST Invoice</label>
                  </div> -->

                  <div class="form-group">
                  <label class="">Type</label>
                <select class="form-control" name="type" id="type" onchange="setIsGst()">
                     <option value="" >Select any value</option>
                    <option value="WHT" selected>WHT</option>
                    <option value="GST" >GST</option>
                    <option value="Cash">Cash</option>
                  
                   
                  </select>
                  </div> 

                <div class="form-group">
                  <input type="checkbox" name="posted" value="1" id="posted" class=""  checked>
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
                  <select class="form-control select2" name="vendor_id" id="vendor_id" onchange="setGrns()">
                    <option value="">Select any vendor</option>
                    @foreach($vendors as $vendor)
                    <option value="{{$vendor['id']}}" >{{$vendor['name']}}</option>
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
                  <input type="text" name="dc_no" class="form-control" value=""  style="width: 100%;">
                  </div>

                <div class="form-group">
                  <label>Dc Date</label>
                  <input type="date" name="dc_date" class="form-control" value=""  style="width: 100%;">
                  </div> -->

                    <div class="form-group">
                  <label>GRN</label>
                  <div class="row">
                    <div class="col-md-9">
                   <select class="form-control select2" name="grn_id" id="grn_id" style="width: 100%;">
                    <option value="">Select any GRN</option>
                    @foreach($grns as $grn)
                    <option value="{{$grn['id']}}">{{$grn['grn_no'].'~~ '.$grn['doc_date']}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <button style="" form="load_po_items" class="btn btn-secondary" onclick="loadGRN()">Load</button>
              </div>
                  </div>

                  <!-- <div class="form-group">
                  <label>Project</label>
                   <select class="form-control" name="project" id="project" style="width: 100%;">
                    <option value="">Select any value</option>
                    <option value="">1</option>
                    <option value="">2</option>
                  </select>
                  </div>
 -->
                   <div class="form-group">
                  <label>Remarks</label>
                  <textarea name="remarks" class="form-control" ></textarea>
                </div>
         </div>

                     
                        </div>

                      
             

              </div>
              <!-- /.col -->

              <div class="col-md-3">


                    <div class="form-section">
                   <h4 class="form-section-title"><i class="fas fa-calculator mr-2"></i>Net Bill</h4>
                  
                  <div class="row">


                   <!--  <div class="col-sm-6">
                    <div class="form-group">
                  <label>Discount %</label>
                  <input type="number" step="any" name="net_discount" id="net_discount" class="form-control" value="" onchange="setNetTaxAndExpense()" style="width: 100%;">
                  </div>
                  </div>


                 <div class="col-sm-6">
                <div class="form-group">
                  <label>Discount</label>
                  <input type="number" step="any" name="discount_amount" id="discount_amount" class="form-control" value="" readonly style="width: 100%;">
                  </div>
                </div> -->

                <div class="col-sm-6">
                    <div class="form-group">
                  <label>GST %</label>
                  <input type="number" step="any" name="gst" id="gst" class="form-control" value="" onchange="setNetTaxAndExpense()" style="width: 100%;">
                  </div>
                  </div>


                  <div class="col-sm-6">
                <div class="form-group">
                  <label>GST Amount</label>
                  <input type="number" step="any" name="gst_amount" id="gst_amount" class="form-control" value="" readonly style="width: 100%;">
                  </div>
                </div> 
                

                    <div class="col-sm-6">
                    <div class="form-group">
                  <label>W.H Tax %</label>
                  <input type="number" step="any" name="net_tax" id="net_tax" class="form-control" value="" onchange="setNetTaxAndExpense()" style="width: 100%;">
                  </div>
                  </div>


                  <div class="col-sm-6">
                <div class="form-group">
                  <label>Tax Amount</label>
                  <input type="number" step="any" name="deduct_amount" id="deduct_amount" class="form-control" value="" readonly style="width: 100%;">
                  </div>
                </div> 

                


                

              </div>

                   <div class="form-group">
                  <label>Net Bill</label>
                  <input type="number" step="any" name="net_bill" id="net_bill" class="form-control" value="" style="width: 100%;">
                  </div>
                   

                    
                 
                    </div>

                      
              </div> 
              <!-- /.col -->

            </div>
            <!-- /.row -->

           



<!-- Start Tabs -->
<div class="form-section mb-4 p-3">
<div class="nav-tabs-wrapper mb-3">
    <ul class="nav nav-tabs dragscroll horizontal">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabA"><i class="fas fa-list mr-2"></i>Detail</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabB"><i class="fas fa-dollar-sign mr-2"></i>Expense</a></li>
        <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabC"><i class="fas fa-money-bill mr-2"></i>Cash Payment</a></li> -->
        
    </ul>
</div>

<span class="nav-tabs-wrapper-border" role="presentation"></span>

<div class="tab-content" style="">
    <div class="tab-pane fade show active" id="tabA">
      
  

               
                
                 <div class="form-section-in border p-3 mb-3" >
                   <h4 class="form-section-title">Add Item</h4>


              
    
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
        <input class="form-control"  name="item_code" id="item_code">
      
    
 
  
</div>
</div>

             <div class="col-md-2">
                    <div class="form-group">
                  <label>Grn No</label>
                  <input type="text" form="add_item" name="grn_no" id="grn_no" onchange="" class="form-control"  style="width: 100%;" readonly>
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
                  <input type="number" min="1" value="1" form="add_item" name="qty" id="qty" onchange="setTotalQty()" class="form-control" required="true" style="width: 100%;">
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
                  <label>Total Qty</label>
                  <input type="number" form="add_item" name="total_qty" id="total_qty" min="1" value="1"  class="form-control" required="true" readonly style="width: 100%;">
                </div>
              </div>

              <div class="col-md-2">
                    <div class="form-group">
                  <label>Current Rate</label>
                  <input type="number" min="0" value="0" form="add_item" name="current_rate" id="current_rate" onchange="setRate()" class="form-control" required="true" style="width: 100%;">
                </div>
              </div>

               <div class="col-md-1">
                    <div class="form-group">
                  <label>Pack Rate</label>
                  <input type="number" form="add_item" min="0" value="0" name="pack_rate" id="pack_rate" onchange="set_rate()" class="form-control" readonly style="width: 100%;">
                </div>
              </div>

              <div class="col-md-1">
                    <div class="form-group">
                  <label>Disc %</label>
                  <input type="number" min="0" value="0" form="add_item" name="disc" id="disc" onchange="discChange()" class="form-control" required="true" style="width: 100%;">
                </div>
              </div>

              <div class="col-md-2">
                    <div class="form-group">
                  <label>Rate</label>
                  <input type="number" form="add_item" name="rate" id="rate" onchange="" class="form-control" readonly style="width: 100%;">
                </div>
              </div>

             

              <div class="col-md-2">
                    <div class="form-group">
                  <label>Total</label>
                  <input type="number" form="add_item" name="total" id="total" onchange="" class="form-control"  style="width: 100%;">
                </div>
              </div>

              <div class="col-md-1">
                    <div class="form-group">
                  <label>Tax %</label>
                  <input type="number" min="0" value="0" form="add_item" name="tax" id="tax" onchange="setTotalAndNet()" class="form-control"  style="width: 100%;">
                </div>
              </div>

               <div class="col-md-2">
                    <div class="form-group">
                  <label>Net Total</label>
                  <input type="text" form="add_item" name="net_total" id="net_total" onchange="" class="form-control" readonly style="width: 100%;">
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



              

     
        <div class="table-responsive">
        <table class="table table-bordered table-hover" id="item_table" >
        <thead class="table-primary">
           <tr>
              <th>No.</th>
             <th>Location</th>
             <th>Code</th>
             <th>Item</th>
             <th>Grn No</th>
             
             <th>UOM</th>
             <th>Unit</th>
             <th>Qty</th>
        
              <th>Pack Size</th>
               
             
             <th>Total Qty</th>
             <th>Current Rate</th>
             <th>Pack Rate</th>
             <th>Disc %</th>
             <th>Rate</th>
             <th>Total(PKR)</th>
             <th>Tax %</th>
            
             <th>Net Total</th>
             <th></th>
           </tr>
        </thead>
        <tbody id="selectedItems">
          
        </tbody>
        <tfoot class="table-secondary">
          <tr>
            
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th id="net_qty">0</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            
            <th id="net_total_amount">0</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
       </div>
        
    </div>
    <div class="tab-pane fade" id="tabB">

      <div class="row">
              <div class="col-md-7">
               
                   <div class="form-section-in border p-3 mb-3" >
                   <h4 class="form-section-title">Add Expenses</h4>
              
    
   <div id="expense_add_error" style="display: none;"><p class="text-danger" id="expense_add_error_txt"></p></div>
      
                   
                   <div class="row">

                    <div class="col-md-5">
                    <div class="form-group mb-0">
                  <label>Expenses</label>
                  <select class="form-control select2" form="add_expense" name="expense" id="expense" style="width: 100%;">
                    <option value="">Select any value</option>
                    @foreach($expenses as $depart)
                    <option value="{{$depart['id']}}">{{$depart['name']}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
               
               

              

              <div class="col-md-1 bt-col">
                    <div class="form-group mb-0">
                    <br>
                  <button type="button" form="add_expense" class="btn" id="add_expense_btn" style="width: 100%;" onclick="addExpense()">
                  <span class="fa fa-plus-circle text-info"></span>
                  </button>
                </div>
              </div>


</div> <!--end row-->



                 </div>
              
                  
                  

                   <table class="table table-hover" >

                  <thead class="table-primary">
                <tr class="">
                <th class="">Expenses</th>
                <th class="">Amount</th>
                <th></th>
                </tr>
                    </thead>   

                    <tbody id="expenses_body" >   

                  


                    </tbody>


                  </table>


                
               
                
                
              </div>
              
        </div>
      
    </div>

    <!-- <div class="tab-pane fade" id="tabC">

       <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Text</label>
                  <input type="text" name="salary" value="{{old('salary')}}" class="form-control" style="width: 100%;">
                </div>
              </div>
        </div>
      
    </div> -->

    

    
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

var row_num=1;
var expense_row_num=1;

function getRowNum()
 {
  return this.row_num;
}

function setRowNum()
 {
     this.row_num++;
}

function getExpenseRowNum()
 {
  return this.expense_row_num;
}

function setExpenseRowNum()
 {
     this.expense_row_num++;
}

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

   $('.select2').select2(); 

  

  var items=[] ;

 setInventory(items);
  
  
  $('#purchase_order_form').submit(function(e) {

    e.preventDefault();
//alert(this.row_num);
var rows=getRowNum();
for (var i =1; i <= rows ;  i++)
{
if ($(`#item_id_${i}`). length > 0 )
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
     $("#p_s").val('');

     document.getElementById('pack_rate').setAttribute('readonly', 'readonly');
       $("#pack_rate").val('');

      
        //setTotalQty();


   }
   else if( unit=='pack')
   {
    document.getElementById('p_s').removeAttribute('readonly');
    document.getElementById('pack_rate').removeAttribute('readonly');
    }
    setTotalAndNet();
}



function setTotalQty()
{
  qty=$("#qty").val();
  p_s=$("#p_s").val();
  total_qty=qty;

  if(p_s!='' )
     total_qty=total_qty*p_s;
   
   $("#total_qty").val(total_qty); 
    
    discChange();
   setTotalAndNet();

}

function setTotalAndNet()
{
   var total_qty=$("#total_qty").val();
   var rate=$("#rate").val();
     if(total_qty=='')
    total_qty=0;
  if(rate=='')
  {
    rate=0;
  }

   var total=parseFloat(rate) * parseFloat(total_qty) ;
   total=total.toFixed(2); 

   $("#total").val(total);
  
  var tax=$("#tax").val();
  if(tax=='')
    tax=0;
    tax_amount=total *(tax/100);
   var net_total= parseFloat(total) + parseFloat (tax_amount) ;
   net_total=net_total.toFixed(2);
   $("#net_total").val(net_total);  

   
}







function setRate()
{
  $("#pack_rate").val('');
   var current_rate=$("#current_rate").val();
   
     var disc=$("#disc").val();
   var rate=0;
   
   if(disc=='')
         disc=0;
       if(current_rate=='')
         current_rate=0;

        

       var d=current_rate * ( disc / 100 );
       rate=current_rate-d;
       rate=rate.toFixed(2);

       $("#rate").val(rate); 

     setTotalAndNet();

}

function set_rate()
{
  $("#current_rate").val('');
   var pack_rate=$("#pack_rate").val();
   var p_s=$("#p_s").val();
     var disc=$("#disc").val();
   var rate=0;
   
   if(disc=='')
         disc=0;
       if(pack_rate=='')
         pack_rate=0;

        var per=pack_rate / p_s ;

       var d=per * ( disc / 100 );
       rate=per - d;
       rate=rate.toFixed(2);

       $("#rate").val(rate); 

     setTotalAndNet();

}

function discChange()
{
   var current_rate=$("#current_rate").val();
    var pack_rate=$("#pack_rate").val();
     var disc=$("#disc").val();

   var rate=0;
   
   if(disc=='')
         disc=0;
       if(current_rate=='')
         current_rate=0;

       if(pack_rate=='')
         pack_rate=0;

       if(current_rate==0 && pack_rate==0)
        {
          $("#total").val('0');
          $("#net_total").val('0');
        }
        else if(current_rate!=0 && pack_rate==0)
        {
              setRate();
        }
        else if(current_rate==0 && pack_rate!=0)
        {
          set_rate();
        }
        else
        {
          set_rate();
        }

}



function checkItem(row='')
{
  var rows=getRowNum();
  for (var i =1; i <= rows ;  i++) {
   
     if ($(`#code_${i}`). length == 0 || $(`#code_${i}`). length < 0 )
     {
      continue;
     }
     if (row == i  )
     {
      continue;
     }
     var item_code=$("#item_code").val();

    

     var tbl_item_code=$(`#code_${i}`).val(); 

     if(item_code == tbl_item_code)
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
  var grn_no=$("#grn_no").val();

  var p_s=$("#p_s").val();
  var total_qty=$("#total_qty").val();
  var current_rate=$("#current_rate").val();
  var pack_rate=$("#pack_rate").val();

  var disc=$("#disc").val();
  var rate=$("#rate").val();
  var total=$("#total").val();
  var tax=$("#tax").val();
  var net_total=$("#net_total").val();

  var dbl_item=false;
  if(item_code!='')
  {
     //dbl_item=checkItem(row);
  }
     
     if(item_code==''  || unit=='' || qty=='' || dbl_item==true)
     {
        var err_name='',err_unit='',err_qty='', err_dbl='';
           
           if(item_code=='')
           {
                err_name='Item is required.';
           }
           
           if(unit=='')
           {
            err_unit='Unit  is required.';
           }
           if(qty=='')
           {
            err_qty='Quantity is required.';
           }
           if(dbl_item==true)
           {
            err_dbl='Item already added.';
           }



           $("#item_add_error").show();
           $("#item_add_error_txt").html(err_dbl+' '+err_name+'  '+err_unit+' '+err_qty);

     }
     else
     {

       $(`#code_${row}`).val(item_code);

 

                      $(`#location_id_${row}`).val(location);
                      $('#location').val(location_text);

                    $(`#unit_${row}`).val(unit);

                         $(`#qty_${row}`).val(qty);

                            $(`#item_grn_${row}`).val(grn_no);

                           $(`#p_s_${row}`).val(p_s);
 
                         $(`#total_qty_${row}`).val(total_qty);
  
                             $(`#current_rate_${row}`).val(current_rate);
  
                        $(`#pack_rate_${row}`).val(pack_rate);
 
                       $(`#disc_${row}`).val(disc);
                        $(`#rate_${row}`).val(rate);
  
                            $(`#total_${row}`).val(total);
  
                             $(`#tax_${row}`).val(tax);
  
                         $(`#net_total_${row}`).val(net_total);
  

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
  $("#grn_no").val('');
  
  $("#item_unit").val('');
  $("#item_code").val('');
  $("#unit").val('loose');
  $("#qty").val('1');

  $("#p_s").val('1');
  $("#total_qty").val('1');
  $("#current_rate").val('');
  $("#pack_rate").val('');

  $("#disc").val('');
  $("#rate").val('');
  $("#total").val('');
  $("#tax").val('');
  $("#net_total").val('');

  $('#row_id').val('');

  $("#item_add_error").hide();
           $("#item_add_error_txt").html('');

            $('#add_item_btn').attr('onclick', `addItem()`);
       setNetQtyAndAmount();
   }

  }// update item

function addItem()
{
  
  var item_code=$("#item_code").val();
  
  //var item_id=$("#item_id").val();
  var location=$("#location").val();
   var location_text=$("#location option:selected").text();
   if(location=='')
    location_text='';
  //var size=$("#item_size").val();
  //var color=$("#item_color").val();
  //var uom=$("#item_unit").val();
//var code=$("#item_code").val();
  var unit=$("#unit").val();
  var qty=$("#qty").val();
  var grn_no=$("#grn_no").val();

  var p_s=$("#p_s").val();
  var total_qty=$("#total_qty").val();
  var current_rate=$("#current_rate").val();
  var pack_rate=$("#pack_rate").val();

  var disc=$("#disc").val();
  var rate=$("#rate").val();
  var total=$("#total").val();
  var tax=$("#tax").val();
  var net_total=$("#net_total").val();

  var dbl_item=false;
  if(item_code!='')
  {
     //dbl_item=checkItem();
  }
     
     if(item_code==''  || unit=='' || qty=='' || dbl_item==true)
     {
        var err_name='',err_unit='',err_qty='', err_dbl='';
           
           if(item_code=='')
           {
                err_name='Item is required.';
           }
           
           if(unit=='')
           {
            err_unit='Unit  is required.';
           }
           if(qty=='')
           {
            err_qty='Quantity is required.';
           }
           if(dbl_item==true)
           {
            err_dbl='Item already added.';
           }



           $("#item_add_error").show();
           $("#item_add_error_txt").html(err_dbl+' '+err_name+'  '+err_unit+' '+err_qty);

     }
     else
     {


      var rows = getRowNum(); 
            
     var txt=`<tr ondblclick="(editItem(${rows}))" id="${rows}"><td></td><td><input type="text"  id="location_${rows}" name="locations[]" form="purchase_order_form" value="${location_text}" readonly ><input type="hidden"  id="location_id_${rows}" name="location_ids[]" form="purchase_order_form" value="${location}"  ></td><td><input type="text" form="purchase_order_form"  id="code_${rows}" name="codes[]" value="${item_code}" readonly ></td><td><input type="text" form="purchase_order_form"  id="item_name_${rows}" name="items_name[]" form="purchase_order_form" value="" readonly ><input type="hidden" form="purchase_order_form"  id="item_id_${rows}" name="items_id[]" value="" readonly ></td>

     <td><input type="text" form="purchase_order_form"  id="item_grn_${rows}" name="items_grn[]" form="purchase_order_form" value="${grn_no}" readonly ><input type="hidden" form="purchase_order_form" name="items_stock[]"  value=""</td>

     <td><input type="text" form="purchase_order_form" id="uom_${rows}" name="" value="" readonly ></td>
     <td><input type="text" form="purchase_order_form"  id="unit_${rows}" name="units[]" value="${unit}" readonly ></td><td><input type="text"  id="qty_${rows}" form="purchase_order_form" name="qtys[]" value="${qty}" readonly ></td>
     <td><input type="text"  id="p_s_${rows}" form="purchase_order_form" name="p_s[]" value="${p_s}" readonly ></td>
     <td><input type="text"  id="total_qty_${rows}" form="purchase_order_form" name="total_qty[]" value="${total_qty}" readonly ></td>
       <td><input type="text" form="purchase_order_form" id="current_rate_${rows}" name="current_rate[]" value="${current_rate}" readonly ></td><td><input type="text" form="purchase_order_form" id="pack_rate_${rows}" name="pack_rate[]" value="${pack_rate}" readonly ></td><td><input type="text" form="purchase_order_form" id="disc_${rows}" name="disc[]" value="${disc}" readonly ></td><td><input type="text"  id="rate_${rows}" name="rate[]" form="purchase_order_form" value="${rate}" readonly ></td>
          <td><input type="text" form="purchase_order_form" id="total_${rows}" name="total[]" value="${total}" readonly ></td>
          <td><input type="text" form="purchase_order_form" id="tax_${rows}" name="tax[]" value="${tax}" readonly ></td>
          <td><input type="text"  id="net_total_${rows}" form="purchase_order_form" name="net_total[]" value="${net_total}" readonly ></td>
     

         <td><button type="button" class="btn" onclick="removeItem(${rows})"><span class="fa fa-minus-circle text-danger"></span></button></td>
     </tr>`;

   

    $("#selectedItems").append(txt);

  
                  
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
  $("#grn_no").val('');
  
  
  
  $("#item_unit").val('');
  $("#item_code").val('');
  $("#unit").val('loose');
  $("#qty").val('1');

  $("#p_s").val('1');
  $("#total_qty").val('1');
  $("#current_rate").val('');
  $("#pack_rate").val('');

  $("#disc").val('');
  $("#rate").val('');
  $("#total").val('');
  $("#tax").val('');
  $("#net_total").val('');

  $('#row_id').val('');

  $("#item_add_error").hide();
           $("#item_add_error_txt").html('');

            setNetQtyAndAmount();
            setRowNum(); 
   
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

if(unit=='pack')
{
   document.getElementById('p_s').removeAttribute('readonly');
    document.getElementById('pack_rate').removeAttribute('readonly');
}

  var qty=$(`#qty_${row}`).val();
$('#qty').val(qty);

var grn_no=$(`#item_grn_${row}`).val();
$('#grn_no').val(grn_no);


  var p_s=$(`#p_s_${row}`).val();
  $('#p_s').val(p_s);
  var total_qty=$(`#total_qty_${row}`).val();
  $('#total_qty').val(total_qty);
  var current_rate=$(`#current_rate_${row}`).val();
  $('#current_rate').val(current_rate);
  var pack_rate=$(`#pack_rate_${row}`).val();
  $('#pack_rate').val(pack_rate);
  var disc=$(`#disc_${row}`).val();
  $('#disc').val(disc);
  var rate=$(`#rate_${row}`).val();
  $('#rate').val(rate);
  var total=$(`#total_${row}`).val();
  $('#total').val(total);
  var tax=$(`#tax_${row}`).val();
  $('#tax').val(tax);
  var net_total=$(`#net_total_${row}`).val();
  $('#net_total').val(net_total);

  $('#row_id').val(row);

  $('#add_item_btn').attr('onclick', `updateItem(${row})`);

}

function removeItem(row)
{
  
  $('#item_table tr').click(function(){
    //$(this).remove();
    $(`#${row}`).remove();
    setNetQtyAndAmount();
  
});

}



function setNetQtyAndAmount()
{
  var rows=getRowNum();
   
   var net_qty=0.0 , net_amount=0.0;   
   for (var i =1; i <= rows ;  i++) { 
    
    if($(`#total_qty_${i}`). length == 0 || $(`#total_qty_${i}`). length < 0)
      continue;
    
       var qty=$(`#total_qty_${i}`).val();
      var amount=$(`#net_total_${i}`).val();
       
       
       

      if(qty=='' || qty==null)
        qty=0;
      if(amount=='' || amount==null)
        amount=0;

        net_qty +=  parseFloat (qty) ;

        net_amount +=  parseFloat (amount) ;

   }
   $(`#net_qty`).text(net_qty);
      $(`#net_total_amount`).text(net_amount);

      setNetTaxAndExpense();
      

}



function setNetTaxAndExpense()
{
  //discount
   // var discount=$('#net_discount').val();
   // if(discount=='')
   //  discount=0;

     var net_amount=$(`#net_total_amount`).text();
      if(net_amount=='')
       net_amount=0;

   //   var discount_amount=(discount / 100) * net_amount;
   //     discount_amount=discount_amount.toFixed(2);

   //   $(`#discount_amount`).val(discount_amount);

   //   var net= parseFloat( net_amount ) - parseFloat( discount_amount) ;
   //   $(`#net_bill`).val(net);

   // GST tax
   var gst=$('#gst').val();
   if(gst=='')
    gst=0;

   
     var gst_amount=(gst / 100) * net_amount;
       gst_amount=gst_amount.toFixed(2);

     $(`#gst_amount`).val(gst_amount);

     

     //tax
   var tax=$('#net_tax').val();
   if(tax=='')
    tax=0;

   
     var deduct_amount=(tax / 100) * net_amount;
       deduct_amount=deduct_amount.toFixed(2);

     $(`#deduct_amount`).val(deduct_amount);

      net=parseFloat(net_amount) + parseFloat(gst_amount) - deduct_amount ;
     $(`#net_bill`).val(net); 

     //expense
        var row=getExpenseRowNum();
     
  for (var i = 1; i <= row; i++) {
      if($(`#exp_debit_${i}`). length == 0 || $(`#exp_debit_${i}`). length < 0)
      continue;
        var debit=$(`#exp_debit_${i}`).val(); 
        net= parseFloat(net) + parseFloat(debit); 
  }

  
          net=net.toFixed(2);
  $(`#net_bill`).val(net);

}



function loadGRN()
{
  var grn_id = jQuery('#grn_id').val();

  if(grn_id!='')
  {

  $.ajax({
               type:'get',
               url:'{{ url("/get/grn/") }}',
               data:{
                    
                     grn_id : grn_id, 

               },
               success:function(data) {

                order_id=data['id'];
                vendor_id=data['vendor_id'];
                vendor_name=data['vendor_name'];

                var expenses=data['expenses'];

              

                //$('#vendor_name').val(vendor_name);
                //$('#vendor_id').val(vendor_id);
                //$('#vendor_id').trigger('change');
                $('#po_id').val(order_id);

                $("#selectedItems").append('');

                for (var i =0; i < data['items'].length  ; i++) {
                  var item = data['items'][i];

                  var rows = getRowNum(); 

     var txt=`<tr ondblclick="(editItem(${rows}))" id="${rows}"><td></td><td><input type="text"  id="location_${rows}" name="locations[]" form="purchase_order_form" value="${item['location']}" readonly ><input type="hidden"  id="location_id_${rows}" name="location_ids[]" form="purchase_order_form" value="${item['location_id']}"  ></td><td><input type="text" form="purchase_order_form"  id="code_${rows}" name="codes[]" value="${item['item_code']}" readonly ></td>
     
     <td><input type="text" form="purchase_order_form"  id="item_name_${rows}" name="items_name[]" form="purchase_order_form" value="${item['item_name']}" readonly ><input type="hidden" form="purchase_order_form"  id="item_id_${rows}" name="items_id[]" value="${item['item_id']}" readonly ></td>

     <td><input type="text" form="purchase_order_form"  id="item_grn_${rows}" name="items_grn[]" value="${item['grn_no']}" readonly ><input type="hidden" form="purchase_order_form" name="items_stock[]"  value="${item['stock_id']}" ></td>

     <td><input type="text" form="purchase_order_form" id="uom_${rows}" name="" value="${item['item_uom']}" readonly ></td>
     <td><input type="text" form="purchase_order_form"  id="unit_${rows}" name="units[]" value="${item['unit']}" readonly ></td><td><input type="text"  id="qty_${rows}" form="purchase_order_form" name="qtys[]" value="${item['qty']}" readonly ></td>
     <td><input type="text"  id="p_s_${rows}" form="purchase_order_form" name="p_s[]" value="${item['pack_size']}" readonly ></td>
     <td><input type="text"  id="total_qty_${rows}" form="purchase_order_form" name="total_qty[]" value="${item['total_qty']}" readonly ></td>
       <td><input type="text" form="purchase_order_form" id="current_rate_${rows}" name="current_rate[]" value="${item['current_rate']}" readonly ></td><td><input type="text" form="purchase_order_form" id="pack_rate_${rows}" name="pack_rate[]" value="${item['pack_rate']}" readonly ></td><td><input type="text" form="purchase_order_form" id="disc_${rows}" name="disc[]" value="${item['discount']}" readonly ></td><td><input type="text"  id="rate_${rows}" name="rate[]" form="purchase_order_form" value="${item['rate']}" readonly ></td>
          <td><input type="text" form="purchase_order_form" id="total_${rows}" name="total[]" value="${item['total']}" readonly ></td>
          <td><input type="text" form="purchase_order_form" id="tax_${rows}" name="tax[]" value="${item['tax']}" readonly ></td>
          <td><input type="text"  id="net_total_${rows}" form="purchase_order_form" name="net_total[]" value="${item['net_total']}" readonly ></td>
     

         <td><button type="button" class="btn" onclick="removeItem(${rows})"><span class="fa fa-minus-circle text-danger"></span></button></td>
     </tr>`;

   

    $("#selectedItems").append(txt);
         setRowNum();
                }

                //setNetQtyAndAmount();

                for (var i =0; i < expenses.length  ; i++) {

                  var rows = getExpenseRowNum();


            var txt=`
             <div class="col-sm-12" id="exp_${rows}">
                <div class="form-group row">
                  <label class="col-sm-3">${expenses[i]['name']}</label>
                  <input type="hidden" name="expense_ids[]" form="purchase_order_form" id="exp_id_${rows}" value="${expenses[i]['id']}" >
                  <input type="number" step="any" value="${expenses[i]['pivot']['amount']}" name="exp_debit[]" class="form-control col-sm-3 m-1" form="purchase_order_form" id="exp_debit_${rows}"   onchange="setNetTaxAndExpense()" style="width: 100%;">
                  
                    <button type="button" class="btn" onclick="removeExpense(${rows})"><span class="fa fa-minus-circle text-danger"></span></button>
                  </div>
                 </div>
            `;
     

   

    $("#expenses_body").append(txt);

  

            setExpenseRowNum(); 


                }

setNetQtyAndAmount();

                
               }

             });//end ajax
}
     
}// end load item


function addExpense()
{
  
  
  var expense=$("#expense").val();
   var expense_text=$("#expense option:selected").text();
   if(expense=='')
      return ;


  var dbl_item=false;
  if(item_code!='')
  {
     dbl_item=checkExpense();
  }
     
     if( dbl_item==true)
     {
        var  err_dbl='';
           
           
           if(dbl_item==true)
           {
            err_dbl='Expense already added.';
           }



           $("#expense_add_error").show();
           $("#expense_add_error_txt").html(err_dbl);

     }
     else
     {


      var rows = getExpenseRowNum();


         


             var txt=`
             <tr class="" id="exp_${rows}">
               
                  <td class="">${expense_text}</td>

                   <td class="">
                  <input type="hidden" name="expense_ids[]" form="purchase_order_form" id="exp_id_${rows}" value="${expense}" >
                  <input type="number" step="any" value="0" name="exp_debit[]" class="form-control col-sm-3 m-1" form="purchase_order_form" id="exp_debit_${rows}"   onchange="setNetTaxAndExpense()" style="width: 100%;">
                  </td>

                   <td class="">
                    <button type="button" class="btn" onclick="removeExpense(${rows})"><span class="fa fa-minus-circle text-danger"></span></button>
                   </td>

                 </tr>
            `;

     

   

    $("#expenses_body").append(txt);

  

           $("#expense_add_error").hide();
           $("#expense_add_error_txt").html('');

           $("#expense").val('');
            setExpenseRowNum(); 
   
   }


     
}//add expense

function checkExpense(row='')
{
  var rows=getExpenseRowNum();
  for (var i =1; i <= rows ;  i++) {
   
     if ($(`#exp_${i}`). length == 0 || $(`#exp_${i}`). length < 0 )
     {
      continue;
     }
     if (row == i  )
     {
      continue;
     }
     var expense=$("#expense").val();
    

     var tbl_exp=$(`#exp_id_${i}`).val(); 
//alert(expense+' '+tbl_exp);
     if(expense == tbl_exp)
      return true;
     
  
   }
  return false;   
   
}


function removeExpense(row)
{
  
 
    $(`#exp_${row}`).remove();
    setNetQtyAndAmount();
  

}

function setIsGst()
{
   
    // var is=$('#is_gst').prop("checked");
    // if(is==false)
    // {
    //   $('#gst').val(0);
    //    //document.getElementById('gst').addAttribute('readonly');

    //    $("#gst").attr("readonly","true");
    //   }
    //   else
    //   {
    //     $("#gst").removeAttr("readonly");
    //   }

    // setNetQtyAndAmount();
    var type=$('#type').val();
   
   $.ajax({
               type:'get',
               url:'{{ url("/get/purchase/code") }}',
               data:{
                    
                    // "_token": "{{ csrf_token() }}",
                    
                     type: type ,
                     
               },
               success:function(data) {

                code=data;
                 
                 $('#purchase_no').val(code);
                 



               }
             });
    
}//end set is gst

function setGrns()
{
   var vendor_id=$('#vendor_id').val();
   var grns=`<?php echo json_encode( $grns )?>`;

   grns=JSON.parse(grns);

   $('#grn_id').empty();

   $("#grn_id").append("<option value='' >Select any GRN</option>");

   for (var i =0; i < grns.length ;  i++) {
     
     if(grns[i]['vendor_id'] == vendor_id || vendor_id=='' )
    {
     $("#grn_id").append("<option value='"+grns[i]['id']+"' >"+grns[i]['grn_no']+'~~'+grns[i]['doc_date']+"</option>");
     $('#grn_id').trigger('change');
     } 
   }
  
}

</script>


@endsection  
  