
@extends('layout.master')
@section('title', 'User')
@section('header-css')

  
@endsection
@section('content-header')
    <!-- Content Header (Page header) -->
<form role="form" id="ticket_form" method="post" action="{{url('/configuration/user/save')}}" autocomplete="off">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">User</h1>
            <button type="submit" style="border: none;background-color: transparent;"><span class="fas fa-save">&nbsp</span>Save</button>
            <button type="reset" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Clear</button>
            <a class="btn" href="{{url('configuration/users')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Users</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">User</a></li>
              <li class="breadcrumb-item active">Add</li>
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
                <h3 class="card-title">Add New User</h3>
              
              </div>
              <!-- /.card-header -->
              <div class="card-body">

                @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible col-md-3">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('success') }}
    </div>
             @endif

             @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible col-md-6">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('error') }}
    </div>
             @endif

                <input type="hidden" form="ticket_form" value="{{csrf_token()}}" name="_token"/>
                
                
                <div class="col-md-6 form-row">

                  <div class="col-sm-12">
                <div class="form-group row">
                  <label class="col-sm-4">User Login</label>
                  <input type="text" form="ticket_form" name="user_login" class="form-control select2 col-sm-8"   required style="width: 100%;">
                  </div>
                 </div>

                <div class="col-sm-12">
                <div class="form-group row">
                  <label class="col-sm-4">Name</label>
                  <input type="text" form="ticket_form" name="name" class="form-control select2 col-sm-8" required style="width: 100%;">
                  </div>
                 </div>

                 <div class="col-sm-12">
                <div class="form-group row">
                  <label class="col-sm-4">Password</label>
                  <input type="password" form="ticket_form" name="password" class="form-control select2 col-sm-8" required style="width: 100%;">
                  </div>
                 </div>


                 <div class="col-sm-12">
                <div class="form-group row">
                  <label class="col-sm-4">Employee</label>
                  <select form="ticket_form" name="employee_id" class="form-control select2 col-sm-8"  style="width: 100%;">
                  <option value="" >select any employee</option>
                    @foreach($employees as $emp)
                    <option value="{{$emp['id']}}" >{{$emp['name']}}</option>
                    @endforeach
                  </select>
                  </div>
                 </div>




                

                  <div class="col-sm-12">
                <div class="form-group row">
                  <label class="col-sm-4">Role</label>
                  <select form="ticket_form" name="roles[]" class="form-control select2 col-sm-8" multiple required style="width: 100%;">
                  
                    @foreach($roles as $role)
                    <option value="{{$role['id']}}" >{{$role['name']}}</option>
                    @endforeach
                  </select>
                  </div>
                 </div>

                 <div class="col-sm-12">
                  <div class="form-group row">
                  
                  <label class="col-sm-8 offset-sm-4"><input type="checkbox" form="ticket_form" name="activeness" value="active" id="activeness" class=""  checked>&nbsp&nbspActive</label>
                  </div>
                </div> 

               

               </div>
 

                 

                



              
                  
                  
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            </form>        



        
      </div>

    <!-- /.content -->
   
@endsection

@section('jquery-code')







@endsection  
  