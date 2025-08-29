
@extends('layout.master')
@section('title', 'Edit Target')
@section('header-css')
<style type="text/css">
  .alert-success {
     color: #155724; 
    background-color: #d4edda;
    /*border-color: #c3e6cb;*/
}

 
</style>

<link href="{{asset('public/own/inputpicker/jquery.inputpicker.css')}}" rel="stylesheet" type="text/css">

@endsection
@section('content-header')
    <!-- Content Header (Page header) -->
    <form role="form" id="purchase_demand" method="POST" action="{{url('update/target')}}" onkeydown="return event.key != 'Enter';" >
      <input type="hidden" value="{{csrf_token()}}" name="_token"/>
      <input type="hidden" value="{{$target['id']}}" name="id"/>

      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">Edit Target</h1>
            <button form="purchase_demand" type="submit" style="border: none;background-color: transparent;"><span class="fas fa-save">&nbsp</span>Update</button>
            <!-- <button type="reset" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Clear</button> -->
            <a class="btn" href="{{url('target/history')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>History</a>
            <a class="btn" href="{{url('target/create')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>New</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Target</a></li>
              <li class="breadcrumb-item active">Edit</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
  @endsection

@section('content')
    <!-- Main content -->

<style type="text/css">
  .alert-inline{
              display: inline;
              color: #d32535;
              background-color:transparent ;
              border:none;padding: .7rem 4rem 0rem 0rem;
     }
</style>
    
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
     
      <div id="error" style="display: none;"><p class="text-danger" id="error_txt"></p></div>


            <div class="row">
              <div class="col-md-4">
                  
                  <fieldset class="border p-4">
                   <legend class="w-auto">Document</legend>

                <!-- /.form-group -->
                <div class="form-group row">
                  <label class="col-sm-4">Doc No.</label>
                  <input type="text" form="purchase_demand" name="doc_no" class="form-control  col-sm-8" value="{{$target['doc_no']}}"  required style="width: 100%;">
                  </div>



                <div class="form-group row">
                  <label class="col-sm-4">Doc Date</label>
                  <input type="date" form="purchase_demand" name="doc_date" class="form-control col-sm-8" value="{{$target['doc_date']}}" required style="width: 100%;">
                  </div>

                  <div class="form-group row">
                  <label class="col-sm-4">Start Date</label>
                  <input type="date" form="purchase_demand" name="start_date" class="form-control col-sm-8" value="{{$target['start_date']}}" required style="width: 100%;">
                  </div>


                  <div class="form-group row">
                  <label class="col-sm-4">End Date</label>
                  <input type="date" form="purchase_demand" name="end_date" class="form-control col-sm-8" value="{{$target['end_date']}}"  style="width: 100%;">
                  </div>

                 


                <div class="form-group">
                  <input type="checkbox" form="purchase_demand" name="active" value="1" id="activeness" class=""  >
                  <label>Active</label>
                  </div>

                  <div class="form-group">
                  <input type="checkbox" form="purchase_demand" name="closed" value="1" id="closed" class=""  >
                  <label>Closed</label>
                  </div>




              </fieldset>

                                
                <!-- /.form-group -->
               
                
              </div>
              <!-- /.col -->
              <div class="col-md-5">

                 <fieldset class="border p-4">
                   <legend class="w-auto">Detail</legend>
                  

                  <div class="form-group">
                  <label>Doctor</label>
                  <select form="purchase_demand" name="doctor_id" id="doctor_id" class="form-control select2" onchange="" required>
                     <option value="">Select any doctor</option>
                     @foreach($doctors as $dt)
                     <?php 

                       $s='';
                         if($dt['id']==$target['doctor_id'])
                          $s='selected';
                     ?>
                     <option value="{{$dt['id']}}" {{$s}}>{{$dt['name']}}</option>
                     @endforeach
                  </select>
                  </div>

                  <div class="form-group">
                  <label>Investment Amount</label>
                  <input type="number" step="any" form="purchase_demand" name="investment_amount" class="form-control" value="{{$target['investment_amount']}}"  >
                  </div>

                  <div class="form-group">
                  <label>Target Value</label>
                  <input type="number" step="any" form="purchase_demand" name="target_value" class="form-control" value="{{$target['target_value']}}"  >
                  </div>



                   <div class="form-group">
                  <label>Remarks</label>
                  <textarea name="remarks" form="purchase_demand" class="form-control" >{{$target['remarks']}}</textarea>
                </div>

                


                     
                        </fieldset>

                      
             

              </div>
              <!-- /.col -->

              <div class="col-md-3">
                  
                  <fieldset class="border p-4">
                   <legend class="w-auto">Target Sale</legend>
                    <?php
                          $total_quantity=$target->total_quantity();
                         $total_amount=$target->target_value();
                     ?>

                <div class="form-group">
                  <label>Net Value</label>
                  <input type="number" form="purchase_demand" name="net_bill" id="net_bill" class="form-control" value="{{$total_amount}}" readonly  style="width: 100%;">
                  </div>

                


              </fieldset>

                                
                <!-- /.form-group -->
               
                
              </div>

                            <!-- /.col -->

            </div>
            <!-- /.row -->

            

            <div class="row">
              <div class="col-md-12">
                
                <fieldset class="border p-4">
                   <legend class="w-auto">Add Item</legend>

              
    
    <div id="item_add_error" style="display: none;"><p class="text-danger" id="item_add_error_txt"></p></div>
      
                   
                   <div class="row">

                    
                  <div class="col-md-3">
                    <label>Items</label>
                 <select class="form-control select2" onchange="" form="add_item" name="item_id" id="item_id_0" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    @foreach($items as $depart)
                    <option value="{{$depart['id']}}">{{$depart['item_name']}}</option>
                    @endforeach
                  </select>
           </div>

           

                 

              <div class="col-md-1">
                    <div class="form-group">
                  <label>Qty</label>
                  <input type="number" value="1" min="1" form="add_item" name="qty" id="qty_0" onchange="rate_change(0)" class="form-control" required="true" style="width: 100%;">
                </div>
              </div>

              
             

              <div class="col-md-2">
                    <div class="form-group">
                  <label>Rate</label>
                  <input type="number" value="" min="0" step="any" form="add_item" name="rate" id="rate_0" onchange="rate_change(0)" class="form-control"  style="width: 100%;">
                </div>
              </div>

              <div class="col-md-2">
                    <div class="form-group">
                  <label>Total</label>
                  <input type="number" value="" min="0" step="any" onchange="set_rate(0)" form="add_item" name="total" id="total_0" class="form-control"  style="width: 100%;">
                </div>
              </div>


            

              <div class="col-md-1">
                    <div class="form-group">
                  <br>
                  <input type="hidden" name="row_id" id="row_id" >
                  <button type="button" form="add_item" id="add_item_btn" class="btn" onclick="addItem()">
    <span class="fa fa-plus-circle text-info"></span>
  </button>
                </div>
              </div>

              


</div> <!--end row-->



                 </fieldset>
                 
              </div>
            </div>



<!-- Start Tabs -->
<div class="nav-tabs-wrapper">
    <ul class="nav nav-tabs dragscroll horizontal">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabA">Targeted Items</a></li>
    </ul>
</div>

<span class="nav-tabs-wrapper-border" role="presentation"></span>

<div class="tab-content" style="background-color: white;border: 1px solid #dee2e6;padding: 10px;min-height:200px;">
    
    <div class="tab-pane fade show active" id="tabA">

      
      <div class="table-responsive p-0" style="height: 400px;">
      <table class="table table-bordered table-hover table-head-fixed text-nowrap"  id="item_table" >
        <thead class="table-secondary">
           <tr>

             <th>#</th>
         
             <th>Item</th>
             <th>Qty</th>
             <th>Rate</th>
             <th>Total</th>

             <th></th>
           </tr>
        </thead>
        <tbody id="selectedItems">
         
         <?php $row=1; ?>
          @foreach($target['items'] as $item)


          <tr id="{{$row}}">
      <th ondblclick=""></th>
     
     <input type="hidden" form="purchase_demand" id="{{'item_id_'.$row}}" name="items_id[]" value="{{$item['id']}}" readonly >


     
     
     <td ondblclick="" id="{{'item_name_'.$row}}">{{$item['item_name']}}</td>


  


     <td><input type="number" value="{{$item['pivot']['qty']}}" min="1" form="purchase_demand" name="qtys[]" id="{{'qty_'.$row}}" onchange="rate_change('{{$row}}')" ></td>

     <td><input type="number" value="{{$item['pivot']['rate']}}" min="0" form="purchase_demand" name="rates[]" id="{{'rate_'.$row}}" onchange="rate_change('{{$row}}')" ></td>

     <?php $total=$item['pivot']['qty'] * $item['pivot']['rate']; ?>
     <td><input type="number" value="{{$total}}" min="0" form="purchase_demand" name="totals[]" id="{{'total_'.$row}}" onchange="set_rate('{{$row}}')"  ></td>
     
      

      
     
     

         <td><button type="button" class="btn" onclick="removeItem('{{$row}}')"><span class="fa fa-minus-circle text-danger"></span></button></td>
     </tr>
           

           <?php $row++; ?>
          @endforeach

          
          
        </tbody>
        <tfoot>
          <tr>

             <th></th>
             <th></th>
             
             <th  id="net_qty">{{$total_quantity}}</th>
             
             <th></th>
             
             <th id="net_total">{{$total_amount}}</th>
             
             <th></th>
           </tr>
        </tfoot>
      </table>
    </div>

        
    </div>
   
    
   

   
</div>
<!-- End Tabs -->
                   



        
      </div>

      </form>
    <!-- /.content -->

    <form role="form" id="#add_item">
              
            </form>
   
@endsection

@section('jquery-code')
<script src="{{asset('public/own/inputpicker/jquery.inputpicker.js')}}"></script>


<script src="{{asset('public/own/table-resize/colResizable-1.6.js')}}"></script>

<script type="text/javascript">

var row_num="{{$row}}";


$(document).ready(function(){

  $('.select2').select2(); 



  $("#item_table").colResizable({
     resizeMode:'overflow'
   });

  value1="{{$target['activeness'] }}";
   
   
   if(value1=="1")
   {
    
  $('#activeness').prop("checked", true);
   
   }
    else{
      $('#activeness').prop("checked", false);
  
    } 

    value1="{{$target['closed'] }}";
   
   
   if(value1=="1")
   {
    
  $('#closed').prop("checked", true);
   
   }
    else{
      $('#closed').prop("checked", false);
  
    } 

  

//   $('#purchase_demand').submit(function(e) {

//     e.preventDefault();
// //alert(this.row_num);
// var row_num=getRowNum();

// for (var i =1; i <= row_num ;  i++) {
// if ($(`#qty_${i}`). length > 0 )
//      {
//         $('#error_txt').html('');
//                this.submit();

//                return ;
//       }
//   }
            
//                $('#error').show();
//                $('#error_txt').html('Select items!');
             
//   });

  



   
});

 function getRowNum()
 {
  return this.row_num;
}

function setRowNum()
{
  this.row_num +=1;
}
  









 




function rate_change(row)
{
        var qty=$(`#qty_${row}`).val();
        if(qty=='')
          qty=0;

        var rate=$(`#rate_${row}`).val();
        if(rate=='')
          rate=0;


           var total=(qty * rate).toFixed(2);
           $(`#total_${row}`).val(total);

   if(row!=0)
   setNetQty();

  }

  function set_rate(row)
{
        var qty=$(`#qty_${row}`).val();
        if(qty=='')
          qty=0;

        var total=$(`#total_${row}`).val();
        if(total=='')
          total=0;


           var rate=(total / qty ).toFixed(2);
           $(`#rate_${row}`).val(rate);
     if(row!=0)
   setNetQty();

  }




    

 





function setNetQty() //for end of tabel to show net
{
  var rows=getRowNum();
  
   var  net_qty=0 , net_total=0;  
   for (var i =1; i <= rows ;  i++) {
   
     if ($(`#total_${i}`). length > 0 )
     { 
    
       var qty=$(`#qty_${i}`).val();
       var total=$(`#total_${i}`).val();
    

      if(qty=='' || qty==null)
        qty=0;

      if(total=='' || total==null)
        total=0;


      
         net_qty +=  parseFloat (qty) ;

         net_total +=  parseFloat (total);//alert(net_total);

        }
         

   }  // alert(net_total);

   net_total =parseFloat (net_total).toFixed(2);
          
  
   $(`#net_qty`).text(net_qty);
   $(`#net_total`).text(net_total);

   
      
           $(`#net_bill`).val( net_total) ;

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
     var item=$("#item_id_0").val();



     var tbl_item_id=$(`#item_id_${i}`).val(); 

     if(item == tbl_item_id)
      return true;
     
  
   }
  return false;   
   
}

function addItem()
{
  
   var item_id=$("#item_id_0").val();
  
  var item_name=$("#item_id_0 option:selected").text();
  
 
 
  var qty=$("#qty_0").val();
  var rate=$("#rate_0").val();
  var total=$("#total_0").val();

  


    
    var dbl_item=false;
  if(item_id!='')
  {
     dbl_item=checkItem();
  }

 //||  rate=='' || qty==''
     if(item_id==''  || dbl_item==true)
     {
        var err_id='',err_rate='',err_qty='', err_dbl='';
           
           if(item_id=='')
           {
                err_id='Item is required.';
           }
           if(rate=='')
           {
            err_rate='Rate is required.';
           }
           
           if(qty=='')
           {
            err_qty='Quantity is required.';
           }

           if(dbl_item==true)
           {
            err_dbl='Item already added.'; alert('d');
           }



           $("#item_add_error").show();
           $("#item_add_error_txt").html(err_dbl+' '+err_id+' '+err_rate+' '+err_qty);

     }
     else
     {
      
        
     var row=getRowNum();   


     var txt=`
     <tr id="${row}">
      <th ondblclick=""></th>
     
     <input type="hidden" form="purchase_demand" id="item_id_${row}" name="items_id[]" value="${item_id}" readonly >


     
     
     <td ondblclick="editItem(${row})" id="item_name_${row}">${item_name}</td>


  


     <td><input type="number" value="${qty}" min="1" form="purchase_demand" name="qtys[]" id="qty_${row}" onchange="rate_change(${row})" ></td>

     <td><input type="number" value="${rate}" min="0" form="purchase_demand" name="rates[]" id="rate_${row}" onchange="rate_change(${row})"  ></td>

     <td><input type="number" value="${total}" min="0" form="purchase_demand" name="totals[]" id="total_${row}" onchange="set_rate(${row})"  ></td>
     
      

      
     
     

         <td><button type="button" class="btn" onclick="removeItem(${row})"><span class="fa fa-minus-circle text-danger"></span></button></td>
     </tr>`;
     
     
      
     
   

    $("#selectedItems").append(txt);

   $( "#item_id_0" ).val('').trigger('change');

  $("#qty_0").val('1');
  $("#rate_0").val('');
  $("#total_0").val('');

  
$('#row_id').val('');

  $("#item_add_error").hide();
           $("#item_add_error_txt").html('');

     
  
          setNetQty();
           setRowNum();
   
   }
   
     
}//end add item




function removeItem(row)
{
  
  $('#item_table tr').click(function(){
    $(`#${row}`).remove();
      setNetQty();
      });

}








</script>

@endsection  
