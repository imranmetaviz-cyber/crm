
@extends('layout.master')
@section('title', 'Target Wise Sales1')
@section('header-css')
<link href="{{asset('public/own/inputpicker/jquery.inputpicker.css')}}" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="{{asset('/public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
  <style type="text/css">
    [data-href] {
    cursor: pointer;
}
td
{
 
}
td{
  
  vertical-align: middle;

}
  </style>

  
@endsection
@section('content-header')
    <!-- Content Header (Page header) -->

      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style="display: inline;">Doctor Sale</h1>

            <?php 

          //  $config=array( 'distributor'=>$distributor,'from'=>$from,'to'=>$to,'so_id'=>$so_id , 'manufactured_by'=>$manufactured_by, 'item_id'=>$item_id );


            $f=''; $t=''; $c_id=''; $d_id='';  $tr='';

            if(isset($config['from']))
              $f=$config['from'];

            if(isset($config['to']))
              $t=$config['to'];


            if(isset($config['doctor']))
              $d_id=$config['doctor']['id'];

            if(isset($distributor))
              $c_id=$distributor['id'];

            if(isset($config['target_wise']))
              $tr=$config['target_wise'];

            

             ?>

            <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" style="border: none;background-color: transparent;" data-toggle="dropdown" >
                      <i class="fa fa-print"></i>&nbsp;Print<i class="caret"></i>
                    </button>
                    <ul class="dropdown-menu">
                
                      <li><a href="{{url('print/doctors/sale?distributor_id='.$c_id.'&from='.$f.'&to='.$t.'&doctor_id='.$d_id.'&target_wise='.$tr)}}" class="dropdown-item">Print</a></li>
                  
                    </ul>
                  </div> 
            
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Doctor</a></li>
              <li class="breadcrumb-item active">Sales</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
  @endsection

@section('content')
    <!-- Main content -->

    
      <div class="container-fluid" style="margin-top: 10px;">

          @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session()->get('success') }}
    </div>
             @endif

         <div class="card">
              <div class="card-header">
                <h3 class="card-title">Target Wise Sales</h3>
                 
                <!-- <a href="{{url('employee/attendance/register-search')}}" class="float-sm-right">Employee Wise Attendance</a> -->
               <div class="card-tools">

                

                  

                </div>

              </div>
              <!-- /.card-header -->
              <div class="card-body">
                 
        <form role="form" id="item_history_form" method="get" action="{{url('doctor/sales/target-wise1')}}">
                   <fieldset class="border p-4">
                   <legend class="w-auto">Criteria</legend>

                       <div id="item_error" style="display: none;"><p class="text-danger" id="item_error_txt"></p></div>

                        <div class="row">

                    
           <div class="col-md-3">
                    <div class="form-group">
                  <label>Distributor</label>
                  <select class="form-control select2"  name="distributor_id" id="distributor_id" onchange="setDoctors()">
                    <option value="">Select any distributor</option>
                    @foreach($distributors as $c)
                       <?php 
                       
                        $s='';
                        if(isset($config['distributor_id']))
                         if($c['id']==$config['distributor_id'])
                          $s='selected';
                     ?>
                    <option value="{{$c['id']}}" {{$s}}>{{$c['name']}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

                  
           

           <div class="col-md-2">
                    <div class="form-group">
                  <label>Doctor</label>
                  <select class="form-control select2"  name="doctor_id" id="doctor_id">
                    <option value="">Select any doctor</option>
                    <?php 
                         // $ds=$doctors;
                         // if(isset($config['distributor_id']))
                         //  $ds=$distributor['doctors'];
                     ?>
                     @if(isset($doctors))
                    @foreach($doctors as $so)
                     <?php 
                       
                        $se='';
                        if(isset($config['doctor_id']))
                         if($so['id']==$config['doctor_id'])
                          $se='selected';
                     ?>
                    <option value="{{$so['id']}}" {{$se}}>{{$so['name']}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>

           <div class="col-md-2">
                    <div class="form-group">
                  <label>From</label>
                  <input type="date" class="form-control"  name="from" id="from" value="@if(isset($config['from'])){{$config['from']}}@endif">
                </div>
              </div>

              <div class="col-md-2">
                    <div class="form-group">
                  <label>To</label>
                  <input type="date" class="form-control"  name="to" id="to"  value="@if(isset($config['to'])){{$config['to']}}@endif" >
                </div>
              </div>

              <?php 
                         $checked='';
                         if(isset($config['target_wise']))
                           if($config['target_wise']=='1')
                          $checked='checked';
                     ?>
              
                    <div class="col-md-2">
                    <label><br><input type="checkbox" class="" name="target_wise" id="target_wise" value="1" {{$checked}}>&nbsp;&nbsp;Target Wise</label>
                     </div>
              

                    <div class="col-md-2">
                      <br>
                    <input type="submit" class="btn btn-info" name="" value="View">
                     </div>


                    </div>

                 </fieldset>
                 </form>
                   @if(isset($targets))<!--Start targets-->
                    
   
                       
                          <table id="example11" class="table table-bordered">
                             <thead>
                              <tr><th colspan="6"><center>{{$distributor['name']}}</center></th></tr>
                                <tr>
                                   <th>Sr No.</th>
                                   <th>Doctor</th>
                                   <th>Investment</th>
                                   <th>Target Sale</th>
                                   <th>Sale Value</th>
                                    <th>Balance Sale</th>
                                </tr>
                             </thead>
                             <tbody>
                              <?php 
                              $i=1; ?>
                              
                            
                            
                              @foreach($targets as $target) <!--start targets-->
                              <?php 
                              $value=$target['doctor']->sale_value($target['start_date'],$target['end_date']); 

                              $bal=$target['target_value']-$value;
                               ?>
                             <tr>
                                    
                                    <td>{{$i}}</td> 
                                    <td>{{$target['doctor']['name']}}</td> 
                                    <td>{{$target['investment_amount']}}</td> 
                                  
                                      <td>{{$target['target_value']}}</td>
                                      <td>{{$value}}</td>
                                      <td>{{$bal}}</td>
                                  </tr>
                                  <?php  //$amount+=$item['amount']; $qty+=$item['qty'];
                                   $i++; ?>  
                                  @endforeach

                                  <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                  </tr>
                                  
                             </tbody>
                          </table>


                  
                   @endif<!--End targets-->


              @if(isset($doctors) && $config['target_wise']!='1')<!--Start targets-->
                    
   
                       
                          <table id="example11" class="table table-bordered">
                             <thead>
                              <tr><th colspan="6"><center>{{$distributor['name']}}</center></th></tr>
                                <tr>

                                    <th>Sr No.</th>
                                   <th>Doctor</th>
                                   <th>Investment</th>
                                   <th>Target Sale</th>
                                   <th>Sale Value</th>
                                    <th>Balance Sale</th>

                                </tr>
                             </thead>
                             <tbody>
                              <?php 
                              $i=1; ?>
                              
                            
                            
                              @foreach($doctors as $doc) <!--start targets-->
                              <?php 
                              $sale=$doc->sale_value($config['from'],$config['to']); 
                               
                               $invest=$doc->investment_value($config['from'],$config['to']); 
                               $target=$doc->target_value($config['from'],$config['to']); 
                                 $bal=$target-$sale;
                               ?>
                             <tr>
                                    
                                    <td>{{$i}}</td> 
                                    <td>{{$doc['name']}}</td> 
                                    <td>{{$invest}}</td> 
                                  
                                      <td>{{$target}}</td>
                                      <td>{{$sale}}</td>
                                      <td>{{$bal}}</td>
                                  </tr>
                                  <?php  //$amount+=$item['amount']; $qty+=$item['qty'];
                                   $i++; ?>  
                                  @endforeach

                                  <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                  </tr>
                                  
                             </tbody>

                             <tfoot>
                               
                                <tr>

                                    <th>Sr No.</th>
                                   <th>Doctor</th>
                                   <th>Investment</th>
                                   <th>Target Sale</th>
                                   <th>Sale Value</th>
                                    <th>Balance Sale</th>

                                </tr>
                             </tfoot>
                          </table>


                  
                   @endif<!--End targets-->

              
                  
                  
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

                   



        
      </div>

    <!-- /.content -->
   
@endsection

@section('jquery-code')

<!-- DataTables  & Plugins -->
<script src="{{asset('public/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('public/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('public/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('public/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, 
      "lengthChange": false,
       "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');


    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });

  jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});

</script>


<script src="{{asset('public/own/inputpicker/jquery.inputpicker.js')}}"></script>
<script type="text/javascript">

  $(document).ready(function(){

$('.select2').select2(); 

$(function () {
  $('[data-tooltip="tooltip"]').tooltip({
    trigger : 'hover'
     });
});

});

 
$(document).ready(function(){
  
  
   

   @if(isset($config['manufactured_by']))
   var manufactured_by=<?php echo json_encode($config['manufactured_by']); ?> ;
   $('#manufactured_by').val(manufactured_by);
   @endif

    @if(isset($config['item_id']))
   var item_id=<?php echo json_encode($config['item_id']); ?> ;
   $('#item_id').val(item_id);
   @endif
     
      
     
   
});

 
function setDoctors()
{

  var distributor_id=$('#distributor_id').val();
  
  var distributorsbutors=JSON.parse( '<?php echo json_encode(  $distributors) ; ?>' );
  
   let point = distributors.findIndex((item) => item.id == distributor_id);
    var doctors=distributors[point]['doctors'];
    
    $('#doctor_id').empty().append('<option value="">Select any doctor</option>');
     
     var txt='';
    for (var i =0; i< doctors.length ;  i++) {
       txt +=`<option value="${doctors[i]['id']}">${doctors[i]['name']}</option>`;
    }
    $('#doctor_id').append(txt);
  
}



</script>
@endsection  
  