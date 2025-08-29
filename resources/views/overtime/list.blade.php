
@extends('layout.master')
@section('title', 'Overtime List')
@section('header-css')

  
@endsection
@section('content-header')
    <!-- Content Header (Page header) -->

      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">Overtime List</h1>
           <!--  <button type="submit" style="border: none;background-color: transparent;"><span class="fas fa-save">&nbsp</span>Save</button>
            <button type="reset" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Clear</button> -->
            <a class="btn" href="{{url('overtime/create')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>New</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Employee</a></li>
              <li class="breadcrumb-item active">Overtimes</li>
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
                <h3 class="card-title">List</h3>
              
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
                    <th>#</th>
                    <th>Doc No</th>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Total Time</th>
                    <th>Remarks</th>
                    <th>Active</th>
                  
                   
                    
                    <th></th>
                  </tr>

                  
            <?php $i=1; ?>
                    @foreach($overtimes as $over)
                        <tr>
                     <td>{{$i}}</td>
                     <td>{{$over['doc_no']}}</td>
                     <td>{{$over['employee']['name']}}</td>
                     <td>{{$over['overtime_date']}}</td>
                    <td>{{$over['start_time']}}</td>
                     <td>{{$over['end_time']}}</td>
                     <td>{{$over['total_time']}}</td>
                     <td>{{$over['remarks']}}</td>
                     <td>{{$over['activeness']}}</td>
                     <td><a href="{{url('overtime/edit/'.$over['id'])}}"><span class="fa fa-edit"></span></a></td>
                   
                    
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
  