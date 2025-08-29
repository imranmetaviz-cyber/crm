
@extends('layout.master')
@section('title', 'Dashboard')
@section('content-header')


    <!-- Content Header (Page header) -->
    
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
  @endsection

@section('content')


        <!-- Main content -->
    
      <div class="container-fluid">

                <!-- <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Title</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                Start creating your amazing application!
              </div>
              <div class="card-footer">
                Footer
              </div>
            </div>
        
          </div>
        </div> -->

         <div class="row" style="margin-top: 20px;">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{$pending_orders}}</h3>

                <p>Pending Orders</p>
              </div>
              <div class="icon">
                <i class="fa fa-shopping-bag"></i>
              </div>
              <a href="{{url('orders/pending')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!--END ROW-->

      </div>
    
    <!-- /.content -->
@endsection    
  