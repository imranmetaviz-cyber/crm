
@extends('layout.master')
@section('title', 'Edit Customer')
@section('header-css')

@endsection
@section('content-header')
    <!-- Content Header (Page header) -->
    

      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">Customer</h1>
            <button type="submit" form="form" style="border: none;background-color: transparent;"><span class="fas fa-save">&nbsp</span>Update</button>
            <button type="reset" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Clear</button>
            <a class="btn" href="{{url('customer/create')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>New</a>
            <a class="btn" href="{{url('customers/list')}}" style="border: none;background-color: transparent;"><span class="fas fa-edit">&nbsp</span>Customers</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Customers</a></li>
              <li class="breadcrumb-item active">Edit Customer</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
  @endsection

@section('content')
    <!-- Main content -->

<form role="form" method="POST" id="form" action="{{url('/customer/update')}}">
      <input type="hidden" value="{{csrf_token()}}" name="_token"/>
      <input type="hidden" value="{{$customer['id']}}" name="id"/>
    
      <div class="container-fluid" style="margin-top: 10px;">

            @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('success') }}
    </div>
             @endif

             
                       @if ($errors->has('msg'))
                                    
                      <div class="alert alert-danger alert-dismissible alert-inline">
                                    <button type="button" class="close" data-dismiss="alert" style="">&times;</button>
                                       {{ $errors->first('msg') }}
                                          </div>

                                          <div class="alert alert-danger alert-dismissible alert-inline">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                       {{ $errors->first('msg') }}
                                          </div>
  
                                @endif
     

            <div class="row">
              <div class="col-md-5">
            
              <fieldset class="border p-4">
                   <legend class="w-auto"></legend>

                <!-- /.form-group -->
                <div class="form-group">
                  <label>Customer Name</label>
                  <input type="text" name="name" class="form-control" value="{{$customer['name']}}" required style="width: 100%;">
                  </div>

                  <div class="form-group">
                  <label>Phone No<span class="text-danger">*</span></label>
                  <input type="text" name="phone" class="form-control" value="{{$customer['phone']}}"  required >
                 
                </div>

                  <div class="form-group">
                  <label>Customer Type</label>
                  <select class="form-control" name="type_id" id="type_id"   >
                    <option value="">------Select any value-----</option>
                    @foreach($types as $type)
                    <option value="{{$type['id']}}">{{$type['text']}}</option>
                    @endforeach
                
                  </select>
                </div>

                 <div class="form-row">

                  <div class="form-group col-sm-7">
                  <label>Contact Person 1<span class="text-danger">*</span></label>
                  <input type="text" name="contact" class="form-control" value="{{$customer['contact']}}"   >
                  </div>

                  <div class="form-group col-sm-5">
                  <label>Mobile<span class="text-danger">*</span></label>
                  <input type="text" name="mobile" class="form-control" value="{{$customer['mobile']}}"   >
                  </div>

                  <div class="form-group col-sm-7">
                  <label>Contact Person 2<span class="text-danger">*</span></label>
                  <input type="text" name="contact2" class="form-control" value="{{$customer['contact2']}}"  >
                  </div>

                  <div class="form-group col-sm-5">
                  <label>Mobile<span class="text-danger">*</span></label>
                  <input type="text" name="mobile2" class="form-control" value="{{$customer['mobile2']}}"   >
                  </div>
                   
                </div>


             
                  


                   <div class="form-group">
                  <label>Email</label>
                  <input type="text" name="email" class="form-control " value="{{$customer['email']}}"  >
                  </div>

                  <div class="form-group">
                  <label>CNIC</label>
                  <input type="text" name="cnic" class="form-control " value="{{$customer['cnic']}}"  style="width: 100%;">
                  </div>

                  <div class="form-group">
                  <label>NTN No.</label>
                  <input type="text" name="ntn" class="form-control" value="{{$customer['ntn']}}"  style="width: 100%;">
                  </div>

                  <div class="form-group">
                  <label>Zip Code</label>
                  <input type="text" name="zip_code" class="form-control" value="{{$customer['zip_code']}}"  style="width: 100%;">
                  </div>


                  <div class="form-group">
                  <label>Address<span class="text-danger">*</span></label>
                  <textarea name="address" class="form-control" required>{{$customer['address']}}</textarea>
                  </div>
                
                <!-- /.form-group -->

                
                 
                 <div class="form-group">
                  <input type="checkbox" name="status" value="1" id="status" class=""  >
                  <label>Active</label>
                  </div>


                
                

                  </fieldset>

               
                
              </div>
              <!-- /.col -->
              <div class="col-md-5">

                
                 <fieldset class="border p-4">
                   <legend class="w-auto"></legend>

                   <div class="form-group">
                  <label>Sales Person</label>
                  <select class="form-control select2" name="so_id"  id="so_id" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    @foreach($sos as $con)
                    <option value="{{$con['id']}}">{{$con['name']}}</option>
                    @endforeach
                  </select>
                  </div>


                  </fieldset>


                  <fieldset class="border p-4">
                   <legend class="w-auto"></legend>

                   <div class="form-group">
                  <label>Credit Days</label>
                  <input type="text" name="credit_days" class="form-control select2" value="{{$customer['credit_days']}}"  style="width: 100%;">
                 
                </div>


                <!-- /.form-group -->
                <div class="form-group">
                  <label>Country</label>
                  <select class="form-control" name="country" onchange="set_states()" id="country" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    @foreach($countries as $con)
                    <option value="{{$con['id']}}">{{$con['name']}}</option>
                    @endforeach
                  </select>
                  </div>

                <div class="form-group">
                  <label>Province</label>
                  <select class="form-control" name="state" id="state" onchange="set_regions()" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    
                  </select>
                  </div>

                  <div class="form-group">
                  <label>Region</label>
                  <select class="form-control " name="region" id="region" onchange="set_districts()" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                   
                  </select>
                </div>

                <div class="row">
                   <div class="col-md-12">
                     
                     <div class="form-group">
                  <label>District</label>
                  <select class="form-control" name="district" id="district" onchange="set_cities()" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                   
                  </select>
                </div>

                <div class="form-group">
                  <label>City</label>
                  <select class="form-control" name="city" id="city" onchange="set_territory()" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Territory</label>
                  <select class="form-control " name="territory" id="territory" onchange="" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                  </select>
                </div>

                

                   </div>
                   <div class="col-md-1">
                     
                     <!-- <div class="form-group">
                  <label>Belt</label>
                  <select class="form-control select2" name="type" id="type" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    <option value="">1</option>
                    <option value="">2</option>
                  </select>
                </div> -->

                <!-- <div class="form-group">
                  <label>Territory</label>
                  <select class="form-control select2" name="type" id="type" style="width: 100%;">
                    <option value="">------Select any value-----</option>
                    <option value="">1</option>
                    <option value="">2</option>
                  </select>
                </div> -->

                <!-- <div class="form-group">
                  <label>Advance Limit<span class="text-danger">*</span></label>
                  <input type="text" name="item_code" class="form-control select2" value="{{old('item_code')}}" required style="width: 100%;">
                 
                </div> -->


                   </div>
                </div>

              

                 
                  </fieldset>    

               


             

              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

                   



        
      </div>

      </form>
    <!-- /.content -->
   
@endsection

@section('jquery-code')

<script type="text/javascript">

  $(document).ready(function(){ 



  $('#form').validate({
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

value1="{{$customer['status'] }}"
   
   
   if(value1=="1")
   {
    
  $('#status').prop("checked", true);
   
   }
    else{
      $('#status').prop("checked", false);
  
    } 

    so_id="{{$customer['so_id'] }}";
    $('#so_id').val(so_id);

     type_id="{{$customer['rate_type_id'] }}";
    $('#type_id').val(type_id);

    setArea();

  });

function setArea() {

  country="{{$customer['country_id'] }}";
    if(country=='' || country==null)
        return ;
      $('#country').val(country);
      set_states();

    state="{{$customer['state_id'] }}";
    if(state=='' || state==null)
        return ;
    $('#state').val(state);
      set_regions();

    region="{{$customer['region_id'] }}";
    if(region=='' || region==null)
        return ;
    $('#region').val(region);
    set_districts();

    district="{{$customer['district_id'] }}";
    if(district=='' || district==null)
        return ;
    $('#district').val(district);
    set_cities();

    city="{{$customer['city_id'] }}";
    if(city=='' || city==null)
        return ;
    $('#city').val(city);
    set_territory();

    territory="{{$customer['territory_id'] }}";
    if(territory=='' || territory==null)
        return ;
    $('#territory').val(territory);

}

function set_states() {

 country= jQuery('#country').val();

 if(country=='')
  return;

    var countries= `<?php echo json_encode($countries); ?>  `;
    countries=JSON.parse(countries);

    let point = countries.findIndex((item) => item.id == country);
         
         var states=countries[point]['provinces'];

           province_text='<option value="">------Select any province-----</option>';
                  for(i=0;i<states.length;i++)
                  {

                    province_text+=`<option value="${states[i]['id']}">${states[i]['name']}</option>`; 

                  }
                  $('#state').html(province_text);


  }// end set_states

  function set_regions() {

    country= jQuery('#country').val();
    province= jQuery('#state').val();

    if(province=='')
      return;

    var countries= `<?php echo json_encode($countries); ?>  `;
    countries=JSON.parse(countries);

    let point = countries.findIndex((item) => item.id == country);
         
         var states=countries[point]['provinces'];

     point = states.findIndex((item) => item.id == province);

    var regions=states[point]['regions'];

           text='<option value="">------Select any value-----</option>';
                  for(i=0;i<regions.length;i++)
                  {

                    text+=`<option value="${regions[i]['id']}">${regions[i]['name']}</option>`; 

                  }
                  $('#region').html(text);

  }// end set_regions

   function set_districts() {

    country= jQuery('#country').val();
    province= jQuery('#state').val();
    region= jQuery('#region').val();

    if(region=='')
      return;

    var countries= `<?php echo json_encode($countries); ?>  `;
    countries=JSON.parse(countries);

    let point = countries.findIndex((item) => item.id == country);
         
         var states=countries[point]['provinces'];

     point = states.findIndex((item) => item.id == province);

    var regions=states[point]['regions'];

    point = regions.findIndex((item) => item.id == region);

    var districts=regions[point]['districts'];

           text='<option value="">------Select any value-----</option>';
                  for(i=0;i<districts.length;i++)
                  {

                    text+=`<option value="${districts[i]['id']}">${districts[i]['name']}</option>`; 

                  }
                  $('#district').html(text);

  }// end set_districts

  function set_cities() {

    country= jQuery('#country').val();
    province= jQuery('#state').val();
    region= jQuery('#region').val();
    district= jQuery('#district').val();

    if(district=='')
      return;

    var countries= `<?php echo json_encode($countries); ?>  `;
    countries=JSON.parse(countries);

    let point = countries.findIndex((item) => item.id == country);
         
         var states=countries[point]['provinces'];

     point = states.findIndex((item) => item.id == province);

    var regions=states[point]['regions'];

    point = regions.findIndex((item) => item.id == region);

    var districts=regions[point]['districts'];

     point = districts.findIndex((item) => item.id == district);

    var cities=districts[point]['cities'];

           text='<option value="">------Select any value-----</option>';
                  for(i=0;i<cities.length;i++)
                  {

                    text+=`<option value="${cities[i]['id']}">${cities[i]['name']}</option>`; 

                  }
                  $('#city').html(text);

  }// end set_cities

  function set_territory() {

    country= jQuery('#country').val();
    province= jQuery('#state').val();
    region= jQuery('#region').val();
    district= jQuery('#district').val();
    city= jQuery('#city').val();

    if(city=='')
      return;

    var countries= `<?php echo json_encode($countries); ?>  `;
    countries=JSON.parse(countries);

    let point = countries.findIndex((item) => item.id == country);
         
         var states=countries[point]['provinces'];

     point = states.findIndex((item) => item.id == province);

    var regions=states[point]['regions'];

    point = regions.findIndex((item) => item.id == region);

    var districts=regions[point]['districts'];

     point = districts.findIndex((item) => item.id == district);

    var cities=districts[point]['cities'];

     point = cities.findIndex((item) => item.id == city);

     var ter=cities[point]['territories'];

           text='<option value="">------Select any value-----</option>';
                  for(i=0;i<ter.length;i++)
                  {

                    text+=`<option value="${ter[i]['id']}">${ter[i]['name']}</option>`; 

                  }
                  $('#territory').html(text);

  }// end set_territory


</script>

@endsection  
  