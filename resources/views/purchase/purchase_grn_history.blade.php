
@extends('layout.master')
@section('title', 'GRN History')
@section('header-css')

  
@endsection
@section('content-header')
    <!-- Content Header (Page header) -->

      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">GRN History</h1>
            <!-- <button type="submit" style="border: none;background-color: transparent;"><span class="fas fa-save">&nbsp</span>Save</button>
            <button type="reset" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Clear</button> -->
            <a class="btn" href="{{url('purchase/goods-receiving-note')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>New</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Purchase GRN</a></li>
              <li class="breadcrumb-item active">History</li>
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
                <h3 class="card-title">GRNs</h3>
              
              </div>
              <!-- /.card-header -->
              <div class="card-body">

                @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible col-md-3">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('success') }}
    </div>
             @endif

               

                

              
                <table id="example1" class="table table-bordered table-hover mt-4" style="">
                  
                  <thead>
                  



                  </thead>
                  <tbody>
                  
                 <tr>
                    <th>Id</th>
                    <th>GRN No</th>
                    <th>Doc Date</th>
                    <th>Vendor</th>
                    <th>Order Doc No</th>
                    <th>Order Date</th>
                    <th>Gross Qty</th>
                    <th>Net Qty</th>
                    
                    <th>Remarks</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>

                  
            
                    @for($i=0;$i< count( $grns) ; $i++)

                   
                      
                        <tr>
                   
                     
                     <td>{{$i+1}}</td>
                     <td>{{$grns[$i]['grn_no']}}</td>
                     <td>{{$grns[$i]['doc_date']}}</td>
                    <td>{{$grns[$i]['vendor_name']}}</td>
                     <td>{{$grns[$i]['order_doc_no']}}</td>
                      <td>{{$grns[$i]['order_date']}}</td>
                     <td>{{$grns[$i]['net_gross']}}</td>
                     <td>{{$grns[$i]['net_quantity']}}</td>
                     <td>{{$grns[$i]['remarks']}}</td>
                     <?php 
                         $s=$grns[$i]['status'];
                     
                      ?>
                     <td>@if($s==0)<span class="badge badge-warning">Pending</span>@endif</td>
                   
                    <td><a href="{{url('edit/purchase/grn/'.$grns[$i]['grn_no'])}}"><span class="fa fa-edit"></span></a></td>
                     
                    </tr>
                   <!--  <tr><td colspan="8"></td></tr> -->

                    @endfor
                
                  
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
  