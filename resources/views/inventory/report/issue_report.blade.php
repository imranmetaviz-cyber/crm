<html>
    <head>
        <title>{{$issue['issuance_no']}}</title>
        <style>
            /** 
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
             **/
            @page {
                margin: 0cm 0cm;
            }

            /** Define now the real margins of every page in the PDF **/
            body {
                margin-top: 5cm;
                margin-left: 0.3cm;
                margin-right: 0.3cm;
                margin-bottom: 2cm;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 10px;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: 5cm;

                /** Extra personal styles **/
                /*background-color: #03a9f4;*/
                /*color: white;*/
                /*text-align: center;*/
                /*line-height: 1.5cm;*/
            }

            /** Define the footer rules **/
            footer {
                position: fixed; 
                bottom: 0cm; 
                left: 0cm; 
                right: 0cm;
                height: 2cm;

                /** Extra personal styles **/
                /*background-color: #03a9f4;
                color: white;
                text-align: center;
                line-height: 1.5cm;*/
                    }

                    .top-heading{ font-size: 14px; }

                    .top-heading-name{ font-size: 12px; text-transform: uppercase;}

                    .top-heading-address{ text-align: center; }

                    .challan-detail td,.challan-detail th{

                      text-align: left;
                      padding-left: 0px;
                    }
             
                    .page_num:after { content: counter(page); }

            .pages:after { content:  counter(pages); }

            .item-table{
             
            width: 100%;
             }

           .col{
             
            padding: 5px;
            text-align: center;
            border-bottom: 1px solid black;
             }

             .col1{
             
            padding: 7px;
            text-align: center;
             }

             .item-name-th{

             	width: 40% ;
             	text-align: left;
                padding-left: 7%;
             }

             .item-name-col{

             	width: 40% ;
             	text-align: left;
                border-bottom: 1px solid black;
             }

             .sign{
              
              border-top: 1px solid black !important;
              
              padding : 10px ;

            }

            .sign span{
                          }
             .from , .to{
                
                border:1px solid black;
                 width: 33%;
                  font-size: 9px;
                height: 100px;
                padding-left: 5 ;
                padding-right: 5;

             }
             .name{
                text-transform: uppercase;
             }
             .address{
                 text-align: center;
             }

             
            
        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
               <table width="100%" style="padding: 10px;" cellspacing="0">
        <tr>
            <td align="" style="width: 34%;">
               
                 <p class="top-heading-name"><strong>{{$company_config['full_name']}}</strong></p>
                <p class="top-heading-address">{{$company_config['factory_address']}}</p>

            </td>
            <td align="center" style="width: 33%">
                
            </td>
            <td align="right"  style="width: 33%;">

               <img src="{{url('public/images/logo.jpg')}}" alt="Logo Image" height="80" width="180">
                
            </td>
        </tr>

        <tr style="background-color:#03a9f4;color: white;"><td style="padding: 10px;" class="top-heading" colspan="3"><strong>Issuance</strong></td></tr>
         
        <tr class="">

            <td align=""  style="width: 50%;">
             <?php 
         $d_date=date_create($issue['issuance_date'] );
           $d_date=date_format($d_date,"d-M-Y");

          ?>
               <table class="challan-detail" style="width: 80%;">
                 <tr><th>Issuance No:</th><td>{{$issue['issuance_no']}}</td></tr>
                 <tr><th>Issuance Date:</th><td>{{$d_date}}</td></tr>
                 <tr><th>Department:</th><td style="text-transform: capitalize;">{{$issue['department']['name']}}</td></tr>
               </table>
                
            </td>

            
            <td align="" style="width: 25%">
                 @if($issue['plan'])
                <table class="challan-detail" style="width: 80%;">
                 <tr><th>Plan No:</th><td>{{$issue['plan']['plan_no']}}</td></tr>
                 <tr><th>Batch No:</th><td>{{$issue['plan']['product']['item_name']}}</</td></tr>
                 <tr><th>Batch No:</th><td>{{$issue['plan']['batch_no']}}</</td></tr>
                 <tr><th>Batch Size:</th><td>{{$issue['plan']['batch_size']}}</</td></tr>
                </table>
                @endif
                
            </td>
            <td align="" style="width: 25%;">
               
            </td>
        </tr>

        

        

    </table>
        </header>

       <script type="text/php">
    if (isset($pdf)) {
    echo $PAGE_COUNT;
        $x =  $pdf->get_width() - 90;
        $y =  $pdf->get_height() - 50;
        $text = "page {PAGE_NUM} of {PAGE_COUNT}";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


    }
</script>

        <footer style="">
    <div>
        <!-- <p>Page <span class="page_num"></span> of <span class="pages"></span> -->
            <div><p><span style="margin-left:60px;">{{date('d-M-Y')}}</span><span style="margin-left:60px;">{{date('H:i:s A')}}</span></p></div>
    </div>
    <div style="height:30px;background-color: #03a9f4;"></div>
  </footer>
        <!-- Wrap the content of your PDF inside a main tag -->
        <main style="width: 100%;">
            <table class="item-table " cellspacing="0" style="margin-top: 5px;">

     <thead style="background-color:#03a9f4;color: white;">
        <tr>
            <th class="col1">Sr No.</th>
            <!-- <th class="col1">Department</th> -->
            <th class="item-name-th">Item</th>
            <th class="col1">Unit</th>
            

            <th class="col1">Request Qty</th>
            <th class="col1">Issued Qty</th>

            <th class="col1">GRN No</th>
            <th class="col1">QC No</th>
        </tr>
        </thead>
        <?php  $i=1; $total=0;   ?>
     @foreach($issue['item_list'] as $item)
     <?php 
          
          $t_qty=$item['quantity']*$item['pack_size']; 

           $total += $t_qty; 
          ?>

        
       <tr>
         <td class="col">{{$i}}</td>
         <!-- <td class="col" style="text-transform: capitalize;">{{$item['location']}}</td> -->
         <td class="item-name-col">{{$item['item']['item_name']}}</td>
         <td class="col">@if($item['item']['unit']!=''){{$item['item']['unit']['name']}}@endif</td>
         <td class="col"></td>
         <td class="col">{{$t_qty}}</td>

          <td class="col">{{$item['grn_no']}}</td>
         <td class="col">{{$item['qc_no']}}</td>



       </tr>
       <?php  $i++;  ?>
       @endforeach

       <tfoot style="border-top: 1px solid black;">
        <tr >
         <td></td>
         <!-- <td></td> -->
         <td></td>
         <td></td>
         <th class="col"></th>
         <th class="col">{{$total}}</th>
         <td></td>
         <td></td>
       </tr>
     </tfoot>

   </table>

   <div style="margin: 20px;">
      <p><b>Remarks:</b></p>
      <p>{{$issue['remarks']}}</p>
   </div>

   <table class="" style="border-spacing:50px; width: 100%;">
      <tr >
         <th class="sign"><span>Issued By</span></th>
         <!-- <th class="sign"><span >Verified By</span></th> -->
         <th class="sign"><span >Received By</span></th>
         <th class="sign"><span>Verified By</span></th>
      </tr>
   </table>


        </main>


        
    </body>
</html>