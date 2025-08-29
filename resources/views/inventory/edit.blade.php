
@extends('layout.master')
@section('title', 'Edit Inventory')
@section('header-css')
@endsection
@section('content-header')
    <!-- Content Header (Page header) -->
   
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">Inventory Item</h1>
            <button type="submit" form="inventory_form"  style="border: none;background-color: transparent;"><span class="fas fa-save">&nbsp</span>Update</button>
            <button type="reset" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Clear</button>
            <a class="btn" href="{{url('inventory/Add')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>New</a>
            <a class="btn" href="{{url('inventory')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>List</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Inventory</a></li>
              <li class="breadcrumb-item active">Edit Item</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
  @endsection

@section('content')
    <!-- Main content -->

 <form role="form" id="inventory_form" method="POST" action="{{url('/update-inventory/'.$inventory['id'])}}">
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

                <!-- /.form-group -->
                <div class="form-group">
                  <label>Department<span class="text-danger">*</span></label>
                  <select class="form-control" onchange="setItemCode()" name="department" id="department" required style="width: 100%;">
                    <option value="">------Select any department-----</option>
                    @foreach($config['departments'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                  </select>
                  </div>


                <div class="form-group">
                  <label>Category</label>
                  <select class="form-control" name="category" id="category" onchange="setItemCode()" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    @foreach($config['categories'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                  </select>
                </div>

                {{---
                <!-----  <div class="form-group">
                  <label>Type</label>
                  <select class="form-control" name="type" id="type" onchange="setItemCode()" style="width: 100%;">
                    <option value="">------Select any type-----</option>
                    @foreach($config['types'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                  </select>
                </div>--->
----}}


                <div class="form-group">
                  <label>Item Code<span class="text-danger">*</span></label>
                  <input type="text" name="item_code" id="item_code" class="form-control" value="{{$inventory['item_code']}}" readonly required style="width: 100%;">
                 
                </div>

                <div class="form-group">
                  <label>Item Name<span class="text-danger">*</span></label>
                  <input type="text" name="item_name" class="form-control" value="{{$inventory['item_name']}}" required style="width: 100%;">
                  </div>


                  <!----<div class="form-group row">
                  <label class="col-md-12">Pack Size<span class="text-danger">*</span></label>
                  <input type="number" step="any" name="pack_size_qty" class="form-control" value="{{$inventory['pack_size_qty']}}" placeholder="14"  style="width: 40%">
                  <input type="text" name="pack_size" class="form-control" value="{{$inventory['pack_size']}}" placeholder="e.g 2x7's"  style="width: 55%;margin-left: 2%;">
                  </div>--->

                   

                   <div class="form-group">
                  <label>Item Bar Code</label>
                  <input type="text" name="item_bar_code" class="form-control " value="{{$inventory['item_bar_code']}}"  style="width: 100%;">
                  </div>

{{-----
                 <!---- <div class="form-group">
                  <label>Generic</label>
                  <input type="text" name="generic" class="form-control " value="{{$inventory['generic']}}"  style="width: 100%;">
                  </div>
                
              
                <div class="form-group">
                  <label>Origin</label>
                  <select name="origin" id="origin" class="form-control" style="width: 100%;">
                    
                    <option value=""  >------Select any value-----</option>
                    @foreach($config['origins'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                    
                  </select>
                </div>----->
                -----}}

                

                <div class="form-group">
                  <label>Unit</label>
                   <select class="form-control " name="unit" id="unit" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    @foreach($config['units'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                  </select>
                  </div>

{{-----
                <!-------- <div class="form-group">
                  <label>GTIN No</label>
                   <select class="form-control " name="gtin_id" id="gtin_id" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    @foreach($config['gtins'] as $gtin) 
                    <?php $s='';
                      //if($gtin['id']==$inventory['gtin_id'])
                        //$s='selected';
                     ?>
                    <option value="{{$gtin['id']}}" {{$s}}>{{$gtin['gtin_no']}}</option>
                    @endforeach 
                  </select>
                  </div> ------->
---}}
               
                
              </div>
              <!-- /.col -->
              <div class="col-md-5">

                 <fieldset class="border p-4">
                   <legend class="w-auto">Stock level</legend>

                   <div class="row">
                   <div class="col-md-4">
                     <label>Minimal</label>
                   </div>
                   <div class="col-md-4">
                     <label>Optimal</label>
                   </div>
                   <div class="col-md-4">
                     <label>Maximal</label>
                   </div>
                   </div>

                    <div class="row">
                   <div class="col-md-4">
                     <input type="number" step="any" class="form-control"  name="minimal" value="{{$inventory['minimal']}}">
                   </div>
                   <div class="col-md-4">
                     <input type="number" step="any" class="form-control" name="optimal" value="{{$inventory['optimal']}}">
                   </div>
                   <div class="col-md-4">
                     <input type="number" step="any" class="form-control" name="maximal" value="{{$inventory['maximal']}}">
                   </div>
                   </div>

                     
                        </fieldset>

                        <fieldset class="border p-4 mt-5">
                        <?php $last_purchase_rate=$inventory->last_purchase_rate();

                        ?>
                      
                    <div class="row">
                   <div class="col-md-6">
                     <label>Purchase Price</label>
                     <input type="text" class="form-control" value="{{$last_purchase_rate}}" name="purchase_price">
                   </div>
                   <div class="col-md-6">
                     <label>MRP</label>
                     <input type="number" step="any" class="form-control" value="{{$inventory['mrp']}}" name="mrp">
                   </div>
                  
                   </div>
                 </fieldset>



                 <!----<fieldset class="border p-4 mt-5">
                    <div class="row">
                   <div class="col-md-6">
                     <label>Small Unit</label>
                  
                
                   <select class="form-control" name="small_unit_id" id="small_unit_id" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    @foreach($config['units'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                  </select>
            
                   </div>
                   <div class="col-md-6">
                     <label>Conversion Rate</label>
                     <input type="number" step="any" class="form-control" name="unit_rate" value="{{$inventory['unit_rate']}}">
                   </div>
                  
                   </div>
                 </fieldset>----->



                 <div class="form-group">
                  <input type="checkbox" name="status" value="1" id="status" class="" >
                  <label>Active</label>
                  </div>

                  <div class="form-group">
                  <input type="checkbox" name="manufactured" value="1" id="manufactured" onchange="" class="" >
                  <label>Manufactured</label>
                  </div>

                  <!----<div class="form-group">
                  <label>Manufactured By</label>
                   <select class="form-control" name="manufactured_by" id="manufactured_by"  style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    <option value="fahmir">Fahmir Pharma</option>
                    <option value="alsehat">Al-Sehat Lab</option>
                    
                  </select>
                  </div>---->

                  <div class="form-group">
                  <label>Bill of Material</label>
                   <select class="form-control" name="procedure" id="procedure"  style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    @foreach($config['procedures'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                  </select>
                  </div>

                <div class="form-group">
                  <label>Remarks</label>
                  <textarea name="remarks" class="form-control" >{{$inventory['remarks']}}</textarea>
                </div>
              
             

              </div>
              <!-- /.col -->

              <div class="col-md-4">

                 <fieldset class="border p-4">
                   <legend class="w-auto">Opening Stock</legend>

                   <div class="row">
                   <div class="col-md-4">
                     <label>Quantity</label>
                   </div>
                   <div class="col-md-4">
                     <label>Rate</label>
                   </div>
                   <div class="col-md-4">
                     <label>Total</label>
                   </div>
                   </div>
                     <input type="hidden" name="stock_id" value="{{$inventory['stock_id']}}">
                    <div class="row">
                   <div class="col-md-4">
                    <?php 

                     $stock=$inventory->item_opening();
                      $qty=''; $grn=''; $batch=''; $total='';
                     if(isset($stock->approved_qty))
                      { 
                        $qty=$stock['approved_qty'];
                        $grn=$stock['grn_no'];
                        $batch=$stock['batch_no'];
                        $total=$qty * $inventory['rate'];
                        $total=round($total,2);
                      }

                      ?>
                     <input type="number" class="form-control" step="any"  name="qty" id="qty" onchange="setTotal()" value="{{$qty}}">
                   </div>
                   <div class="col-md-4">
                     <input type="number" class="form-control" step="any" name="rate" id="rate" onchange="setTotal()" value="{{$inventory['rate']}}">
                   </div>
                   <div class="col-md-4">
                     <input type="number" class="form-control" step="any" name="total" id="total" value="{{$total}}">
                   </div>
                   </div>

                   <div class="row">
                   <div class="col-md-6">
                     <label>GRN No.</label>
                   </div>
                   <div class="col-md-6">
                     <label>Batch No.</label>
                   </div>
                   </div>

                    <div class="row">
                   <div class="col-md-6">
                     <input type="text" class="form-control"  name="grn_no" value="{{$grn}}">
                   </div>
                   <div class="col-md-6">
                     <input type="text" class="form-control" name="batch_no" value="{{$batch}}">
                   </div>
                   </div>

                     
                        </fieldset>
                      </div>


            </div>
            <!-- /.row -->



<!-- Start Tabs -->
<div class="nav-tabs-wrapper">
    <ul class="nav nav-tabs dragscroll horizontal">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabA">Colors</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabB">Packing</a></li>
        
    </ul>
</div>

<span class="nav-tabs-wrapper-border" role="presentation"></span>

<div class="tab-content" style="background-color: white;border: 1px solid #dee2e6;padding: 10px;">
    <div class="tab-pane fade show active" id="tabA">

      <div class="row">

              {{----
              <!-----<div class="col-md-3">
                <div class="form-group">
                  <label>Sizes</label>
                 
                  <select class="form-control" name="size" id="size" style="width: 100%;">
                    <option value="">----Select Size----</option>
                    @foreach($config['sizes'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                  </select>
                </div>
               
             
                
              </div>---->
---}}


              <div class="col-md-3">
                
                <table class="table table-bordered">
                    <thead>
                       <th>#</th>
                       <th>Colors</th>
                       <th></th>
                    </thead>

                    <tbody id="color_body">
                       
                          
                     

                    <?php $color_row=1; ?>
                                
                         @foreach($inventory['colors'] as $row) 
                        <tr id="{{'color_'.$color_row}}"> 
                          <td></td>
                          <td><input type="hidden"  form="inventory_form"  name="color[]" value="{{$row['id']}}">{{$row['name']}}</td>
                          <td><button type="button" class="btn" onclick="removeColor(<?php echo $color_row; ?>)"><span class="fa fa-minus-circle text-danger"></span></button></td>
                        </tr>
                           <?php $color_row++; ?>
                           @endforeach

                           
                        
                    </tbody>

                    <tfoot>
                      <tr>
                          <td></td>
                          <td></td>
                          <td><button type="button" class="btn" data-toggle="modal" data-target="#colorsModal" ><span class="fa fa-plus-circle text-success"></span></button></td>
                        </tr>

                    </tfoot>
                </table>


                     <!-- /color modal -->
          <div class="modal fade" id="colorsModal">
        <div class="modal-dialog">
          <div class="modal-content bg-gradient-info">
            <div class="modal-header">
              <h4 class="modal-title ">Colors</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

               <div class="form-group">
                  <label>Color</label>
                  <select class="form-control"  id="color" style="">
                    <option value="">------Select any value-----</option>
                    @foreach($config['colors'] as $raw)
                    <option value="{{$raw['id']}}">{{$raw['name']}}</option>
                    @endforeach
                    
                  </select>

                </div>

                <span class="text-danger" id="color-error" style="display:none;">This color is already added!</span>

              
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-outline-light" onclick="AddColor()" >Add</button>
              
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.color modal -->

           
               
                  


              </div>
        </div>
        
    </div>
     <div class="tab-pane fade" id="tabB">

      <div class="row">
              <div class="col-md-3">

                <table class="table table-bordered">
                    <thead>
                       <th>#</th>
                       <th>Pack Size</th>
                       <th></th>
                    </thead>

                    <tbody id="packing_body">
                         <?php $packing_row=1; ?>
                                
                         @foreach($inventory['packs'] as $pk) 
                        <tr id="{{'packing_'.$packing_row}}"> 
                          <td></td>
                          <td><input type="number" step="any" name="packing[]" value="{{$pk}}"></td>
                          <td><button type="button" class="btn" onclick="removePacking(<?php echo $packing_row; ?>)"><span class="fa fa-minus-circle text-danger"></span></button></td>
                        </tr>
                           <?php $packing_row++; ?>
                           @endforeach
                           
                        
                    </tbody>

                    <tfoot>
                      <tr>
                          <td></td>
                          <td></td>
                          <td><button type="button" class="btn" onclick="AddPacking()"><span class="fa fa-plus-circle text-success"></span></button></td>
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
   
@endsection

@section('jquery-code')
<script type="text/javascript">


    $('#inventory_form').validate({
    rules: {
      
    },
    messages: {
      
    },
    submitHandler: function (form) {
        //alert("Form submitted successfully!");
        form.submit(); // uncomment for real submission
      },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });


   var packing_row=<?php echo json_encode($packing_row); ?>;

   var color_row=<?php echo json_encode($color_row); ?>;

$(document).ready(function(){
  

   value1="{{ $inventory['department_id'] }}";
   
   if(value1!="")
   {
    
  $('#department').find('option[value="{{$inventory['department_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['small_unit_id'] }}";
   
   if(value1!="")
   {
    
  $('#small_unit_id').find('option[value="{{$inventory['small_unit_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['type_id'] }}";
   
   if(value1!="")
   {
    
  $('#type').find('option[value="{{$inventory['type_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['category_id'] }}";
   
   if(value1!="")
   {
    
  $('#category').find('option[value="{{$inventory['category_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['unit_id'] }}";
   
   if(value1!="")
   {
    
  $('#unit').find('option[value="{{$inventory['unit_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['origin_id'] }}";
   
   if(value1!="")
   {
    
  $('#origin').find('option[value="{{$inventory['origin_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['size_id'] }}";
   
   if(value1!="")
   {
    
  $('#size').find('option[value="{{$inventory['size_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['color_id'] }}";
   
   if(value1!="")
   {
    
  $('#color').find('option[value="{{$inventory['color_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['procedure_id'] }}";
   
   if(value1!="")
   {
    
  $('#procedure').find('option[value="{{$inventory['procedure_id']}}"]').attr("selected", "selected");
   
   }

   value1="{{ $inventory['is_manufactured'] }}";
   
   
   if(value1=="1")
   {
    
  $('#manufactured').prop("checked", true);
   
   }
    else{
      $('#manufactured').prop("checked", false);
  
    } 

   value1="{{ $inventory['status'] }}";
   
   
   if(value1=="1")
   {
    
  $('#status').prop("checked", true);
   
   }
    else{
      $('#status').prop("checked", false);
  
    } 

 value1="{{ $inventory['manufactured_by'] }}";
   
   if(value1!="")
   {
    
  $('#manufactured_by').find('option[value="{{$inventory['manufactured_by']}}"]').attr("selected", "selected");
   
   }



});

function setItemCode()
{
    var department=$('#department').val();
    var type=$('#type').val();
    var category=$('#category').val();

     var department_id="{{ $inventory['department_id'] }}";
      var type_id="{{ $inventory['type_id'] }}";
        var category_id="{{ $inventory['category_id'] }}";

        var item_code="{{ $inventory['item_code'] }}";
        if(department_id==department && type_id==type && category==category_id)
        {
            $('#item_code').val(item_code);
             return;
        }

    $.ajax({
               type:'get',
               url:'{{ url("/get/item/code") }}',
               data:{
                    
                    // "_token": "{{ csrf_token() }}",
                    
                     department: department ,
                     type: type,
                     category: category,
               },
               success:function(data) {

                item_code=data;
                 
                 $('#item_code').val(item_code);
                 



               }
             });

}

function setTotal()
{
   var rate=$('#rate').val();
   var qty=$('#qty').val();

   var total=rate * qty;

   total=total.toFixed(2);

   $('#total').val(total);
}

function AddPacking()
{
      
           var packing_row=this.packing_row;
      
     var txt=`<tr id="packing_${packing_row}"><td></td><td><input type="number" step="any" form="inventory_form"   name="packing[]" value="1" >
       </td>
       <td><button type="button" class="btn" onclick="removePacking(${packing_row})"><span class="fa fa-minus-circle text-danger"></span></button></td></tr>`;

    $("#packing_body").append(txt);
        
       this.packing_row=this.packing_row+1;
   
   
     
}// end function

function removePacking(row)
{
  
  
    $(`#packing_${row}`).remove();
  


}

function AddColor(){
   var color_row=this.color_row;

   let color_id=$('#color').val();
   let color=$('#color option:selected').text();
   
   if(color_id==''){
     return;
   }

   if ($("input[name='color[]'][value='" + color_id + "']").length > 0) {
        $("#color-error").show();
        return;
    }
    else{
      $("#color-error").hide();
    }
      
     var txt=`<tr id="color_${color_row}"><td></td><td><input type="hidden" form="inventory_form"   name="color[]" value="${color_id}" >${color}</td>
       
       <td><button type="button" class="btn" onclick="removeColor(${color_row})"><span class="fa fa-minus-circle text-danger"></span></button></td></tr>`;

    $("#color_body").append(txt);

    $("#colorsModal").modal('hide');
        
       this.color_row=this.color_row+1;
}
function removeColor(row)
{
  
  
    $(`#color_${row}`).remove();
  


}


</script>

@endsection  
  