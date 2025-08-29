
@extends('layout.master')
@section('title', 'Inventories')
@section('header-css')

  
@endsection
@section('content-header')
    <!-- Content Header (Page header) -->

      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">Inventory Configuration</h1>
           <!--  <button type="submit" style="border: none;background-color: transparent;"><span class="fas fa-save">&nbsp</span>Save</button>
            <button type="reset" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Clear</button> -->
            <a class="btn" href="{{url('inventory/Add')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>New</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Configuration</a></li>
              <li class="breadcrumb-item active">Inventories</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
  @endsection

@section('content')
    <!-- Main content -->

    
      <div class="container-fluid" style="margin-top: 10px;">

          

         <div class="card">
              <div class="card-header">
                <h3 class="card-title">Inventory Items</h3>
              
              </div>
              <!-- /.card-header -->
              <div class="card-body">

                @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible col-md-3">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('success') }}
    </div>
             @endif

               
              <form role="form" id="item_history_form" method="get" action="{{url('/inventory')}}">
                   <fieldset class="border p-4">
                   <legend class="w-auto">Criteria</legend>

                       <div id="item_error" style="display: none;"><p class="text-danger" id="item_error_txt"></p></div>

                        <div class="row">

                    

          
           <div class="col-md-2">
                    <div class="form-group">
                  <label>Department</label>
                  <select class="form-control select2"  name="department_id" id="department_id">
                    <option value="">Select any department</option>
                    @foreach($departments as $so)
                    <option value="{{$so['id']}}">{{$so['name']}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

           

                    <div class="col-md-2">
                      <br>
                    <input type="submit" class="btn btn-info" name="" value="View">
                     </div>


                    </div>

                 </fieldset>
                 </form>
                

              
                <table id="example1" class="table table-bordered table-hover mt-4" style="">
                  
                  <thead>
                  
                    <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Department</th>
                    <th>Type</th>
                    <th>Item</th>
                    <th>Code</th>
                    <th>Category</th>
                    <th>Origin</th>
                    <th>Open Qty</th> 
                    <th>Unit</th>
                   
                    <th>MRP</th>
                    <th>Purchase Price</th>
                    <th>Status</th>
                    <th></th>
                  </tr>


                  </thead>
                  <tbody>
                  
                

                  
                   <?php $i=1; ?>
                    @foreach($items as $item)

                    <?php
                  $department='';
                  if(isset($item['department']['name'])) 
                  $department=$item['department']['name'];

                 $category='';
                  if(isset($item['category']['name'])) 
                  $category=$item['category']['name'];
                 
                 $origin='';
                  if(isset($item['origin']['name'])) 
                  $origin=$item['origin']['name'];

                 $unit='';
                  if(isset($item['unit']['name'])) 
                  $unit=$item['unit']['name'];
                  
                   $type='';
                  if(isset($item['type']['name'])) 
                  $type=$item['type']['name'];
                  
                   $size='';
                  if(isset($item['size']['name'])) 
                  $size=$item['size']['name'];
                   
                    $color='';
                  if(isset($item['color']['name'])) 
                  $color=$item['color']['name'];

                    $last_purchase_rate=$item->last_purchase_rate();
                        //$b= json_encode($item['grns']);
                        
                        $open=$item->item_opening();
                          $open_qty=0;
                          if(isset($open['approved_qty']))
                           $open_qty=$open['approved_qty'];
                 ?>

                      
                        <tr>
                   
                     
                     <td>{{$i}}</td>
                     <td>{{$item['id']}}</td>
                     <td>{{$department}}</td>
                     <td>{{$type}}</td>
                    <td>{{$item['item_name']}}</td>
                     <td>{{$item['item_code']}}</td>
                     <td>{{$category}}</td>
                     <td>{{$origin}}</td>
                      <td>{{$open_qty}}</td>   
                     <td>{{$unit}}</td>
                                  
                     <td>{{$item['mrp']}}</td>
                     <td>{{$last_purchase_rate}}</td>
                    <td>
                      @if($item['status']==1)
                       Active
                       @else
                       Inactive
                       @endif
                     </td>
                     <td><a href="{{url('edit/inventory/'.$item['id'])}}"><span class="fa fa-edit"></span></a></td>
                   
                    
                 
                   
                  </tr>
                    <?php $i++; ?>

                    @endforeach
                
                  
                  </tbody>
                  <tfoot>
                  <tr>
                  
                  </tr>
                  </tfoot>
                </table>


              
                  
                  
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

                   



        
      </div>

    <!-- /.content -->
   
@endsection

@section('jquery-code')







@endsection  
  