<html>
    <head>
        <title>{{$purchase['doc_no']}}</title>
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

                    .top-heading-name{ font-size: 12px; text-transform: uppercase; }

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
               
                 <p class="top-heading-name"><strong>{{$name}}</strong></p>
                <p class="top-heading-address">{{$address}}</p>
            </td>
            <td align="center" style="width: 33%">
                
            </td>
            <td align="right"  style="width: 33%;">

               <img src="{{url('public/images/logo.jpg')}}" alt="Logo Image" height="80" width="180">
                
            </td>
        </tr>

        <tr style="background-color:#03a9f4;color: white;"><td style="padding: 10px;" class="top-heading" colspan="3"><strong>PURCHASE INVOICE</strong></td></tr>
         
        <tr class="">

            <td align=""  style="width: 40%;">

               <table class="challan-detail" >
                 <tr><th>Invoice No:</th><td>{{$purchase['doc_no']}}</td></tr>
                 <tr><th>Invoice Date:</th><td>{{$purchase['doc_date']}}</td></tr>
                 <tr><th>Vendor:</th><td>@if($purchase['vendor_id']!=''){{$purchase['vendor']['name']}}@endif</td></tr>
               </table>
                
            </td>

            
            <td align="" style="width: 20%">
                
            </td>
            <td align=""  style="width: 40%;">

               <table class="challan-detail" >
                 <tr><th>PO No:</th><td>@if($purchase['grn']!='')
                    @if($purchase['grn']['order']!='')
                 {{$purchase['grn']['order']['doc_no']}}
                   @endif 
                  @endif</td></tr>
                 <tr><th>PO Date:</th><td>@if($purchase['grn']!='')
                 @if($purchase['grn']['order']!='')
                 {{$purchase['grn']['order']['order_date']}}
                  @endif
                  @endif
                  </td></tr>
                 <tr><th>GRN No:</th><td>@if($purchase['grn_id']!=''){{$purchase['grn']['grn_no']}}@endif</td></tr>
                 <tr><th>GRN Date:</th><td>@if($purchase['grn_id']!=''){{$purchase['grn']['doc_date']}}@endif</td></tr>

               </table>
                
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
            
            <th class="item-name-th">Item</th>
            <th class="col1">Unit</th>
            <th class="col1">Qty</th>
            <th class="col1">Rate</th>
            <th class="col1">Exc S.T Amount</th>
            <th class="col1">Tax Value</th>
            <th class="col1">Inc S.T Amount</th>

        </tr>
        </thead>
        <?php  $i=1; $total_qty=0; $net_total=0; $net_amount=0;  ?>
     @foreach($purchase['items'] as $item)
     <?php 
           $uom='';
           if($item['unit']!='')
            $uom=$item['unit']['name'];
           
           $qty=$item['pivot']['quantity'] * $item['pivot']['pack_size'] ;
           $total_qty += $qty;
             
             $rate=$purchase->rate_exclusive_tax($item['id'],$item['pivot']['id']);
             $total=$rate * $qty;
             $net_total += $total;

             $t_rate=$purchase->rate_inclusive_tax($item['id'],$item['pivot']['id']);
             $amount=$t_rate * $qty;
             $net_amount += $amount;

             $tax=$amount - $total;

          ?>
       <tr>
         <td class="col">{{$i}}</td>
         
         <td class="item-name-col">{{$item['item_name']}}</td>
         <td class="col">{{$uom}}</td>
         <td class="col">{{$qty}}</td>
         <td class="col">{{$rate}}</td>
         <td class="col">{{$total}}</td>
         <td class="col">{{$tax}}</td>
         <td class="col">{{$amount}}</td>
       </tr>
       <?php  $i++;  ?>
       @endforeach

       <tfoot style="border-top: 1px solid black;">
        <tr >
         <td></td>
         <td></td>
         <td></td>
         
         <th class="col">{{$total_qty}}</th>
         <td></td>
         <th class="col">{{$net_total}}</th>
         <td></td>
         <th class="col">{{$net_amount}}</th>
       </tr>
     </tfoot>

   </table>
  <?php 
   $tax=$purchase->with_hold_tax_amount();
   if($tax=='')
    $tax=0;
     
     $gst=$purchase->gst_amount();

  $expenses=$purchase->expenses_amount();
      
      $net=($net_amount + $gst  - $tax ) + $expenses ; 
   ?>
   <table style="width: 40%;float: right;text-align: right;">
    @if($gst!=0)
    <tr><th style="width:60% ">GST Tax:</th> <td style="width: 40%">{{$gst}}</td> </tr>
    @endif
    <tr><th style="width:60% ">{{'W.H Tax '.$purchase['net_tax'].'% :'}}</th> <td style="width: 40%">{{$tax}}</td> </tr>
    @foreach($purchase['expenses'] as $exp)
    <tr><th style="width:60% ">{{$exp['name'].' : '}}</th> <td style="width: 40%">{{$exp['pivot']['debit']}}</td> </tr>
    @endforeach
    <tr><th style="width:60% ">TOTAL:</th> <td style="width: 40%">{{$net}}</td> </tr>
       
   </table>

   <div style="margin: 20px;">
      <p><b>Remarks:</b></p>
      <p>{{$purchase['remarks']}}</p>
   </div>

   <table class="" style="border-spacing:70px; width: 100%;">
      <tr >
         <th class="sign"><span>Prepared & Checked By</span></th>
         <th class="sign"><span >Verified By</span></th>
         <th class="sign"><span >Received By</span></th>
         <th class="sign"><span>Approved By</span></th>
      </tr>
   </table>


        </main>


        
    </body>
</html>