
@extends('layout.master')
@section('title', 'Purchase Orders History')
@section('header-css')

  
@endsection
@section('content-header')
    <!-- Content Header (Page header) -->

      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">Purchase Orders History</h1>
            <!-- <button type="submit" style="border: none;background-color: transparent;"><span class="fas fa-save">&nbsp</span>Save</button>
            <button type="reset" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Clear</button> -->
            <a class="btn" href="{{url('/purchase/order')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>New</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Purchase Orders</a></li>
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
                <h3 class="card-title">Purchase Orders</h3>
              
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
                    <th>Doc No</th>
                    <th>Doc Date</th>
                    <th>Vendor</th>
                    <th>Demand Doc No</th>
                    <th>Demand Date</th>
                    <th>Net Qty</th>
                    <th>Net Amount</th>
                    <th>PO Type</th>
                    <th>Received Date</th>
                    <th>Dispatched Status</th>
                    <th>Remarks</th>
                    <th>Post</th>
                    <th>Action</th>
                  </tr>

                  
            
                    @for($i=0;$i< count( $orders) ; $i++)

                   
                      
                        <tr>
                   
                     
                     <td>{{$orders[$i]['id']}}</td>
                     <td>{{$orders[$i]['doc_no']}}</td>
                     <td>{{$orders[$i]['doc_date']}}</td>
                    <td>{{$orders[$i]['vendor_name']}}</td>
                     <td>{{$orders[$i]['demand_doc_no']}}</td>
                      <td>{{$orders[$i]['demand_date']}}</td>
                     <td>{{$orders[$i]['net_quantity']}}</td>
                      <td>{{$orders[$i]['net_amount']}}</td>
                      <td>{{$orders[$i]['po_type']}}</td>
                     <td>{{$orders[$i]['received_date']}}</td>
                      <td>{{$orders[$i]['dispatched_status']}}</td>
                     <td>{{$orders[$i]['remarks']}}</td>
                     <td>{{$orders[$i]['posted']}}</td>
                   
                    <td><a href="{{url('edit/purchase/order/'.$orders[$i]['doc_no'])}}"><span class="fa fa-edit"></span></a></td>
                     
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
  