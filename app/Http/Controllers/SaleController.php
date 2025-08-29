<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Models\InventoryDepartment;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\sale_stock;
use App\Models\Transection;
use App\Models\rate_type;
use App\Models\Configuration;
use App\Models\salereturn_ledger;
use App\Imports\SalesImport;
use App\Models\inventory;
use App\Models\Expense;
use App\Models\Transportation;
use App\Models\packing_type;
use App\Models\Port;
use App\Models\freight_type;
use App\Models\Currency;
use PDF;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales=Sale::orderBy('invoice_no', 'desc')->get();

        return view('sale.sale_history',compact('sales'));
    }

    public function sale_ledger_summary(Request $request)
    {
         $from=$request->from;
         $to=$request->to;

         
           
           if($from!='' || $to!='')
         $lists=Sale::where('invoice_date','>=',$from)->where('invoice_date','<=',$to)->orderBy('invoice_date','asc')->get();
       else
        $lists=Sale::orderBy('invoice_date','asc')->get();

         $config=array('from'=>$from,'to'=>$to);

         return view('sale.reports.sale_ledger_summary',compact('lists','config'));
    }

    public function get_doc_no(Request $request)
    {
        
            $type=$request->type;

         if($type=='local')
         $doc_no="SI-".Date("y")."-";
         elseif($type=='export')
            $doc_no="FP/EXP-".Date("y")."-";
        $num=1;

         $order=Sale::select('id','invoice_no')->where('invoice_no','like',$doc_no.'%')->orderBy('invoice_no','desc')->where('type',$type)->latest()->first();

         if($order=='')
         {
              $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $order['invoice_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }

          return response()->json($doc_no, 200);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        

        $doc_no="SI-".Date("y")."-";
        $num=1;

         $order=Sale::select('id','invoice_no')->where('invoice_no','like',$doc_no.'%')->orderBy('invoice_no','desc')->where('type','local')->latest()->first();
         if($order=='')
         {
              $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $order['invoice_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }

        $customers=Customer::where('activeness','1')->get();
        
        // $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();
        $its=inventory::where('department_id',1)->where(function($q){
            $q->where('activeness','like','active');
         })->get();

            $inventories=[];
         foreach ($its as $key ) {
             
             $uom='';
             if(isset($key['unit']['name']))
                $uom=$key['unit']['name'];
            
             $q=$key->closing_stock();



        $it=['id'=>$key['id'],'item_name'=>$key['item_name'],'unit'=>$uom,'mrp'=>$key['mrp'],'qty'=>$q,'batches'=>$key['batches']];

             array_push($inventories, $it);
         }

        $salesmen=Employee::where('is_so','1')->where('activeness','active')->orderBy('name')->get();

        $expenses=Expense::where('activeness','1')->orderBy('name','asc')->get();
         $currencies=Currency::where('activeness','1')->get();
     $ports=Port::orderBy('text')->get();
     $freight_types=freight_type::orderBy('text')->get();
     $transportations=Transportation::orderBy('text')->get();
     $packing_types=packing_type::orderBy('text')->get();

        return view('sale.sale',compact('currencies','ports','transportations','packing_types','freight_types','salesmen','inventories','doc_no','customers','expenses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(Request $request)
    {

      $chln=Sale::where('invoice_no',$request->doc_no)->first();
            if($chln!='')
             return redirect()->back()->withErrors(['error'=>'Invoice No. already existed!']);

        $location_ids=$request->location_ids;
       $items_id=$request->items_id;
       $units=$request->units;
        $qtys=$request->qtys;
        $pack_sizes=$request->p_s;
        $mrps=$request->mrp;
        //$tps=$request->tp;
        $batch_nos=$request->batch_no;
        $expiry_dates=$request->expiry_date;
        $rates=$request->rate;
        $discount_types=$request->discount_type;
        $discount_factors=$request->discount_factor;
        $taxs=$request->tax;

         
         $active=$request->active;
        if($active=='')
            $active='0';


        $challan=new Sale;

        $challan->invoice_no=$request->doc_no;
        $challan->invoice_date=$request->doc_date;
        $challan->type=$request->type;
        
        $challan->activeness=$active;
        $challan->customer_id=$request->customer_id;
        $challan->challan_id=$request->challan_id;
         $challan->salesman_id=$request->salesman_id;
         $challan->net_discount=$request->disc;
          $challan->net_discount_type=$request->net_disc;

          $challan->gst=$request->gst;
        $challan->remarks=$request->remarks;

        $challan->currency_id=$request->currency_id;
        $challan->cur_rate=$request->cur_rate;

        $challan->shipment_port_id=$request->shipment_port_id;
        $challan->discharge_port_id=$request->discharge_port_id;
        $challan->packing_type_id=$request->packing_type_id;
        $challan->freight_type_id=$request->freight_type_id;
        $challan->transportation_id=$request->transportation_id;

        $challan->save();
            
            for($i=0;$i<count($items_id);$i++)
            {
              $com_type=''; $com_value='';
              if($challan->salesman!='')
              {
                 $com=$challan->salesman->estimate_commission($challan['customer_id'],$items_id[$i]);
                $com_type=$com['type']; $com_value=$com['value'];
              }
         $challan->items()->attach($items_id[$i] , ['unit' => $units[$i] , 'qty' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i] ,'mrp'=>$mrps[$i] ,'batch_no'=>$batch_nos[$i] ,'expiry_date'=>$expiry_dates[$i],'rate'=>$rates[$i] ,'discount_type'=>$discount_types[$i],'discount_factor'=>$discount_factors[$i] ,'commission_type'=>$com_type,'commission_factor'=>$com_value ]);

           }

        $customer_acc=Customer::find($request->customer_id)->account_id;

           $gst_amount=$challan->gst_amount();
             if($gst_amount!=0)
             {
              $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks='Ref '.$challan['invoice_no'].': Gst Amount';
           $trans->debit=$gst_amount;
           $trans->credit=0;
           $trans->save();

           $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=776;
           //$trans->corporate_id=$item['id'];
           $trans->remarks='Ref '.$challan['invoice_no'].': Gst Amount';
           $trans->debit=0;
           $trans->credit=$gst_amount;
           $trans->save();


             }

           foreach ($challan->sale_stock_list as $item) {

                
               $rate=$challan->rate($item['item']['id'],$item['id']);
               $amount= $rate * ($item['qty'] * $item['pack_size'] );

               $remarks=$item['item']['item_name'].' ('.$rate.'*'.$item['qty'].')';

        
           $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$amount;
           $trans->credit=0;
           $trans->save();

           $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=368;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$amount;
           $trans->credit=0;
           $trans->save();

           $com=$item->commission();
            if($challan->salesman!='' && $com > 0)
              { 
                 $val=$item['commission_factor'];
               
                $remarks='Commission to '.$challan['salesman']['name'].' against '.$item['sale']['customer']['name'].' for '.$item['item']['item_name'].' ('.$item['qty'].')';
                $acc=$challan['salesman']['account_id'];

                     $trans=new Transection;

                     $trans->account_voucherable_id=$challan->id;
                     $trans->account_voucherable_type='App\Models\Sale';
                     $trans->account_id=$acc;
                     //$trans->corporate_id=$item['id'];
                     $trans->remarks=$remarks;
                     $trans->debit=0;
                     $trans->credit=$com;
                     $trans->save();
                    
                     $trans=new Transection;

                     $trans->account_voucherable_id=$challan->id;
                     $trans->account_voucherable_type='App\Models\Sale';
                     $trans->account_id=369;
                     //$trans->corporate_id=$item['id'];
                     $trans->remarks=$remarks;
                     $trans->debit=$com;
                     $trans->credit=0;
                     $trans->save();
                    
                }


           } 


    
           $amount=$challan->net_discount();
           if($amount > 0)
           {
              $remarks="Ref: Discount on Invoice : ".$challan['invoice_no'];
           

         
           $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$amount;
           $trans->save();
         }
       
       //expense
        $expense_ids=$request->expense_ids;
        $exp_amount=$request->exp_amount;
           //start expense

           if($expense_ids!='' || $expense_ids!=null)
            {
           for($i=0;$i<count($expense_ids);$i++)
            {
                 $amount=0; 

              if($exp_amount[$i]!='')
                $amount=$exp_amount[$i];

               $challan->expenses()->attach($expense_ids[$i] , ['amount' => $amount ]);
 
           }
           }

           foreach ($challan['expenses'] as $exp) 
             {
              $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks='Ref '.$challan['invoice_no'].':'.$exp['name'];
           $trans->debit=$exp['pivot']['amount'];
           $trans->credit=0;
           $trans->save();

           $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=775;
           //$trans->corporate_id=$item['id'];
           $trans->remarks='Ref '.$challan['invoice_no'].':'.$exp['name'];
           $trans->debit=0;
           $trans->credit=$exp['pivot']['amount'];
           $trans->save();

             }

        return redirect('/edit/sale/'.$challan['id'])->with('success','Sale genrated!');
                  //return redirect()->back()->with('success','Sale genrated!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        $customers=Customer::where('activeness','1')->get();
        
        // $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();

        $its=inventory::where('department_id',1)->where(function($q){
            $q->where('activeness','like','active');
         })->get();

            $inventories=[];
         foreach ($its as $key ) {
             
             $uom='';
             if(isset($key['unit']['name']))
                $uom=$key['unit']['name'];
            
             $q=$key->closing_stock();



        $it=['id'=>$key['id'],'item_name'=>$key['item_name'],'unit'=>$uom,'mrp'=>$key['mrp'],'qty'=>$q,'batches'=>$key['batches']];

             array_push($inventories, $it);
         }

        $items=array();
             foreach ($sale['items'] as $key ) {
                 
                 $pivot_id=$key['pivot']['id'];
                 $unit=$key['pivot']['unit'];
                 $qty=$key['pivot']['qty'];
                 $pack_size=$key['pivot']['pack_size'];
                 $total_qty=$qty * $pack_size;
                 $mrp=$key['pivot']['mrp'];
                 //$tp=round( (0.85 * $mrp  ),2);
                 $batch_no=$key['pivot']['batch_no'];
                 $expiry_date=$key['pivot']['expiry_date'];
                 $rate=$key['pivot']['rate'];
                 $discount_type=$key['pivot']['discount_type'];
                 $discount_factor=$key['pivot']['discount_factor'];

                 $discounted_value=0;

                 if($discount_type=='flat')
                    $discounted_value=$discount_factor;
                elseif($discount_type=='percentage')
                   $discounted_value=round( (($discount_factor/100)*$rate) ,2);

                  $d_rate=$rate- $discounted_value;
                  $total=round( ($total_qty * $d_rate),2);
                 //$tax=$key['pivot']['tax'];
                  //$tax_amount= round( (($tax/100)*$total),2);

                 // $net_amount = $tax_amount + $total ;

                    $item=array( 'item_id'=>$key['id'] , 'pivot_id'=>$pivot_id , 'location_id'=>$key['department_id'],'location_text'=>$key['department']['name'],'item_name'=>$key['item_name'],'unit'=>$unit,'qty'=>$qty,'pack_size'=>$pack_size,'mrp'=>$mrp,'batch_no'=>$batch_no,'expiry_date'=>$expiry_date,'total_qty'=>$total_qty,'discount_type'=>$discount_type,'discount_factor'=>$discount_factor,'discounted_value'=>$discounted_value,'rate'=>$rate,'discounted_rate'=>$d_rate,'total'=>$total);

                 array_push($items, $item);
             } //print_r(json_encode($items));die;

        $salesmen=Employee::where('is_so','1')->where('activeness','active')->orderBy('name')->get();
         $expenses=Expense::where('activeness','1')->orderBy('name','asc')->get();
           $currencies=Currency::where('activeness','1')->get();
     $ports=Port::orderBy('text')->get();
     $freight_types=freight_type::orderBy('text')->get();
     $transportations=Transportation::orderBy('text')->get();
     $packing_types=packing_type::orderBy('text')->get();
        return view('sale.edit_sale',compact('currencies','ports','transportations','packing_types','freight_types','salesmen','inventories','sale','items','customers','expenses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $location_ids=$request->location_ids;
        $pivot_ids=$request->pivots_id;
       $items_id=$request->items_id;
       $units=$request->units;
        $qtys=$request->qtys;
        $pack_sizes=$request->p_s;

        $mrps=$request->mrp;
        //$tps=$request->tp;
        $batch_nos=$request->batch_no;
        $expiry_dates=$request->expiry_date;
        $discount_types=$request->discount_type;
        $discount_factors=$request->discount_factor;
        $rates=$request->rate;
        $taxs=$request->tax;

         
         $active=$request->active;
        if($active=='')
            $active='0';

        

        $challan=Sale::find($request->id);
          
          $old_salesman_id=$challan['salesman_id'];

        $challan->invoice_no=$request->doc_no;
        $challan->invoice_date=$request->doc_date;
        $challan->type=$request->type;
        $challan->activeness=$active;
        $challan->customer_id=$request->customer_id;
        $challan->challan_id=$request->challan_id;
         $challan->salesman_id=$request->salesman_id;
         $challan->net_discount=$request->disc;
          $challan->net_discount_type=$request->net_disc;
           $challan->gst=$request->gst;
        $challan->remarks=$request->remarks;

         $challan->currency_id=$request->currency_id;
        $challan->cur_rate=$request->cur_rate;

        $challan->shipment_port_id=$request->shipment_port_id;
        $challan->discharge_port_id=$request->discharge_port_id;
        $challan->packing_type_id=$request->packing_type_id;
        $challan->freight_type_id=$request->freight_type_id;
        $challan->transportation_id=$request->transportation_id;

        $challan->save();
            
        $items=sale_stock::where('invoice_id',$challan['id'])->whereNotIn('id',$pivot_ids)->get();

        foreach ($items as $tr) {
                $tr->delete();
        }

           for ($i=0;$i<count($items_id);$i++)
           {
                 if($pivot_ids[$i]!=0)
                 $item=sale_stock::find($pivot_ids[$i]);
                  else
                    $item=new sale_stock;
                  
                if($pivot_ids[$i]==0 || $old_salesman_id!=$challan['salesman_id'])
                  {
                    $com_type=''; $com_value='';
              if($challan->salesman!='')
              {
                 $com=$challan->salesman->estimate_commission($challan['customer_id'],$items_id[$i]);
                $com_type=$com['type']; $com_value=$com['value'];
                $item->commission_type=$com_type;
                $item->commission_factor=$com_value;
              }
            }

            if($challan->salesman=='')
              {
                $item->commission_type='';
                $item->commission_factor='';
              }



                $item->invoice_id=$challan['id'];
                $item->item_id=$items_id[$i];
                $item->unit=$units[$i];
                $item->qty=$qtys[$i];
                $item->pack_size=$pack_sizes[$i];
                $item->mrp=$mrps[$i];
                $item->batch_no=$batch_nos[$i];
                $item->expiry_date=$expiry_dates[$i];
                $item->rate=$rates[$i];
                //$item->business_type=$business_type[$i];
                $item->discount_type=$discount_types[$i];
                $item->discount_factor=$discount_factors[$i];
              
                $item->save();
           }
            
            $transections=$challan->transections;

            // for ($i=0; $i < count($transections) ; $i++) { 
            //   print_r(json_encode($transections[$i]['account_id']));die;
            // }
            $no=0;

            $customer_acc=Customer::find($request->customer_id)->account_id;

           $gst_amount=$challan->gst_amount();
             if($gst_amount!=0)
             {

              if($no < count($transections))
          $trans=$transections[$no];
          else
              $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks='Ref '.$challan['invoice_no'].': Gst Amount';
           $trans->debit=$gst_amount;
           $trans->credit=0;
           $trans->save();
           $no++;
          
          if($no < count($transections))
          $trans=$transections[$no];
          else
           $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=776;
           //$trans->corporate_id=$item['id'];
           $trans->remarks='Ref '.$challan['invoice_no'].': Gst Amount';
           $trans->debit=0;
           $trans->credit=$gst_amount;
           $trans->save();
           $no++;


             }

           foreach ($challan->sale_stock_list as $item) {

                
               $rate=$challan->rate($item['item']['id'],$item['id']);
               $amount= $rate * ($item['qty'] * $item['pack_size'] );

               $remarks=$item['item']['item_name'].' ('.$rate.'*'.$item['qty'].')';

       

         if($no < count($transections))
          $trans=$transections[$no];
          else
           $trans=new Transection;

           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$amount;
           $trans->credit=0;

           $trans->save();
           $no++;
          

           if($no < count($transections))
          $trans=$transections[$no];
          else
           $trans=new Transection;

           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=368;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$amount;
           $trans->credit=0;
           $trans->save();
           $no++;
                 
                 $com=$item->commission();
            if($challan->salesman!='' && $com > 0)
              { 
                 $val=$item['commission_factor'];
                //$remarks='Commission : '.$item['sale']['customer']['name'].' for '.$item['item']['item_name'].' ('.$item['qty'].')';
                $remarks='Commission to '.$challan['salesman']['name'].' against '.$item['sale']['customer']['name'].' for '.$item['item']['item_name'].' ('.$item['qty'].')';
                $acc=$challan['salesman']['account_id'];

                     if($no < count($transections))
                    $trans=$transections[$no];
                    else
                     $trans=new Transection;

                     $trans->account_voucherable_id=$challan->id;
                     $trans->account_voucherable_type='App\Models\Sale';
                     $trans->account_id=$acc;
                     //$trans->corporate_id=$item['id'];
                     $trans->remarks=$remarks;
                     $trans->debit=0;
                     $trans->credit=$com;
                     $trans->save();
                     $no++;



                     if($no < count($transections))
                    $trans=$transections[$no];
                    else
                     $trans=new Transection;

                     $trans->account_voucherable_id=$challan->id;
                     $trans->account_voucherable_type='App\Models\Sale';
                     $trans->account_id=369;
                     //$trans->corporate_id=$item['id'];
                     $trans->remarks=$remarks;
                     $trans->debit=$com;
                     $trans->credit=0;
                     $trans->save();
                     $no++;
                }

           }

             $amount=$challan->net_discount();
           if($amount > 0)
           {
              $remarks="Ref: Discount on Invoice : ".$challan['invoice_no'];
           

        // $customer_acc=Customer::find($request->customer_id)->account_id;
            if($no < count($transections))
          $trans=$transections[$no];
          else
           $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$amount;
           $trans->save();
           $no++;
         }

           
           //expense
        $expense_ids=$request->expense_ids;
        $exp_amount=$request->exp_amount;
           //start expense

        $rel1=array();
            if($expense_ids!='' || $expense_ids!=null)
            {
           for($i=0;$i<count($expense_ids);$i++)
            {
                 $amount=0; 

              if($exp_amount[$i]!='')
                $amount=$exp_amount[$i];

              

              $pivot1=array('amount' => $amount   );

                $let1=array( $expense_ids[$i].'' => $pivot1 );

                $rel1=$rel1+$let1;

                            

               
           } 
           
              $challan->expenses()->sync($rel1);
           }
           else
           {  
            
            $challan->expenses()->detach();

           }

           foreach ($challan['expenses'] as $exp) 
             {

              if($no < count($transections))
          $trans=$transections[$no];
          else
              $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks='Ref '.$challan['invoice_no'].':'.$exp['name'];
           $trans->debit=$exp['pivot']['amount'];
           $trans->credit=0;
           $trans->save();
           $no++;
            

            if($no < count($transections))
          $trans=$transections[$no];
          else
           $trans=new Transection;
           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Sale';
           $trans->account_id=775;
           //$trans->corporate_id=$item['id'];
           $trans->remarks='Ref '.$challan['invoice_no'].':'.$exp['name'];
           $trans->debit=0;
           $trans->credit=$exp['pivot']['amount'];
           $trans->save();
            $no++;

             }

             for($i=$no; $i < count($transections); $i++ )
           {
               $transections[$i]->delete();
           }



                  return redirect()->back()->with('success','Sale Updated!');
    }

    public function estimated_invoice(Sale $sale,$invoice_type)
    {

        $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();
           
          
           $total_net_qty=0;
           $total_amount=0;

           $items=array();
             foreach ($sale['items'] as $key ) {
                 
                 $unit=$key['pivot']['unit'];
                 $qty=$key['pivot']['qty'];
                 $pack_size=$key['pivot']['pack_size'];
                 $total_qty=$qty * $pack_size;
                 $mrp=$key['pivot']['mrp'];
                 $tp=round( (0.85 * $mrp  ),2);
                 $batch_no=$key['pivot']['batch_no'];
                 $expiry_date=$key['pivot']['expiry_date'];
                 $rate=$key['pivot']['rate'];
                 $discount_type=$key['pivot']['discount_type'];
                 $discount_factor=$key['pivot']['discount_factor'];

                 $discounted_value=0;
                 if($discount_type=='flat')
                    $discounted_value=$discount_factor;
                elseif($discount_type=='percentage')
                   $discounted_value=round( (($discount_factor/100)*$rate) ,2);

                  $d_rate=$rate- $discounted_value;
                  $total=round( ($total_qty * $d_rate),2);
                

                  $total_net_qty = $total_net_qty +  $total_qty;
                     $total_amount=$total_amount +  $total;
                  
                   $um='';
                   if(isset($key['unit']['name']))
                    $um=$key['unit']['name'];

                    $item=array('item_id'=>$key['id'],'location_id'=>$key['department_id'],'location_text'=>$key['department']['name'],'item_name'=>$key['item_name'],'um'=>$um,'unit'=>$unit,'qty'=>$qty,'pack_size'=>$pack_size,'mrp'=>$mrp,'tp'=>$tp,'batch_no'=>$batch_no,'expiry_date'=>$expiry_date,'total_qty'=>$total_qty,'discount_type'=>$discount_type,'discount_factor'=>$discount_factor,'discounted_value'=>$discounted_value,'rate'=>$rate,'discounted_rate'=>$d_rate,'total'=>$total);

                 array_push($items, $item);
             }
          $net_discount=$sale['net_discount'];
          $net_discount_type=$sale['net_discount_type'];
           $net_discount_value=0;
            if($net_discount_type=='flat')
                $net_discount_value=$net_discount;
            elseif($net_discount_type=='percentage')
                $net_discount_value=round (( ($net_discount/100) * $total_amount),2);

              $discounted_amount = $total_amount - $net_discount_value ;
              $gst_amount=round(($sale['gst'] /100)*$discounted_amount ,2);

            $net_bill = $discounted_amount + $gst_amount ;

            $expenses=[];

            foreach ($sale['expenses'] as $key ) {
              
              $net_bill=$net_bill+$key['pivot']['amount'];

              array_push($expenses, ['expense'=>$key['name'],'amount'=>$key['pivot']['amount'] ]);
            }

            $sale=array( 'id'=>$sale['id'], 'invoice_no'=>$sale['invoice_no'], 'invoice_date'=>$sale['invoice_date'] , 'customer'=>$sale['customer'] ,'remarks'=>$sale['remarks'],  'items'=>$items , 'challan' => $sale['challan'] ,'total_net_qty'=>$total_net_qty , 'total_amount'=>$total_amount ,'gst_amount'=>$gst_amount , 'net_discount'=>$net_discount , 'net_discount_type'=>$net_discount_type , 'net_discount_value'=>$net_discount_value , 'expenses'=>$expenses , 'net_bill'=>$net_bill );

           
        $data = [
            
            'sale'=>$sale,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        
        ];
        //return view('sale.estimated_invoice',compact('data'));
        if($invoice_type=='invoice')
           {
            view()->share('sale.estimated_invoice',$data);
        $pdf = PDF::loadView('sale.estimated_invoice', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream($sale['invoice_no'].'.pdf');
      }
        elseif($invoice_type=='tp-invoice')
          {
            view()->share('sale.tp_invoice',$data);
        $pdf = PDF::loadView('sale.tp_invoice', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream($sale['invoice_no'].'.pdf');
      }
      elseif($invoice_type=='mrp-invoice')
          {
            view()->share('sale.mrp_invoice',$data);
        $pdf = PDF::loadView('sale.mrp_invoice', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream($sale['invoice_no'].'.pdf');
      }

        
        
    }


    public function export_invoice(Sale $sale)
    {
           
             $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();

           

           
        $data = [
            
            'sale'=>$sale,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        
        ];
    
        
           
            view()->share('sale.export_invoice_rpt',$data);
        $pdf = PDF::loadView('sale.export_invoice_rpt', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream($sale['invoice_no'].'.pdf');
      
        
    }


      public function sale_history(Request $request)
      {
          

          $customer_id=$request->customer_id;
           $customer=Customer::find($customer_id);
           
           $from=$request->from;
           $to=$request->to;
           $so_id=$request->so_id;
           $manufactured_by=$request->manufactured_by;

           $item_id=$request->item_id;

         $customers=Customer::orderBy('name')->where('activeness','1')->get();
          $sos=Employee::sales_man();
           
           $sales=sale_stock::whereHas('sale', function ($q) use ($customer_id,$from,$to){
            $q->where('activeness',1);
               if($customer_id!='')   
                $q->where('customer_id',$customer_id);
                if($from!='')   
                $q->where('invoice_date','>=',$from);
                if($to!='')   
                $q->where('invoice_date','<=',$to);
            })->whereHas('sale.customer', function ($q) use ($so_id){
                  
               if($so_id!='')   
                $q->where('so_id',$so_id);
                
            })->whereHas('item', function ($q) use ($manufactured_by,$item_id){
                  
               if($manufactured_by!='')   
                $q->where('manufactured_by',$manufactured_by);

              if($item_id!='')   
                $q->where('id',$item_id);
                
            })->get();
             

              $depart=InventoryDepartment::find(1);
        
        $items=$depart->inventories->where('activeness','like','active')->sortBy('item_name');


             $config=array( 'customer'=>$customer,'from'=>$from,'to'=>$to,'so_id'=>$so_id , 'manufactured_by'=>$manufactured_by, 'item_id'=>$item_id );
             
         

            return view('sale.sale_ledger',compact('items','sales','sos','customers','config'));
      }


      public function sale_history_print(Request $request)
      {
          

          $customer_id=$request->customer_id;
           $customer=Customer::find($customer_id);
           
           $from=$request->from;
           $to=$request->to;
           $so_id=$request->so_id;
           $manufactured_by=$request->manufactured_by;

           $item_id=$request->item_id;

         //$customers=Customer::orderBy('name')->where('activeness','1')->get();
         // $sos=Employee::sales_man();
           
           $sales=sale_stock::whereHas('sale', function ($q) use ($customer_id,$from,$to){
            $q->where('activeness',1);
               if($customer_id!='')   
                $q->where('customer_id',$customer_id);
                if($from!='')   
                $q->where('invoice_date','>=',$from);
                if($to!='')   
                $q->where('invoice_date','<=',$to);
            })->whereHas('sale.customer', function ($q) use ($so_id){
                  
               if($so_id!='')   
                $q->where('so_id',$so_id);
                
            })->whereHas('item', function ($q) use ($manufactured_by,$item_id){
                  
               if($manufactured_by!='')   
                $q->where('manufactured_by',$manufactured_by);

              if($item_id!='')   
                $q->where('id',$item_id);
                
            })->get();
             

              //$depart=InventoryDepartment::find(1);
        
        //$items=$depart->inventories->where('activeness','like','active')->sortBy('item_name');


             $config=array( 'customer'=>$customer,'from'=>$from,'to'=>$to,'so_id'=>$so_id , 'manufactured_by'=>$manufactured_by, 'item_id'=>$item_id );

              $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();

             $data = [
            
            'sales'=>$sales,
            'config'=>$config,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        
        ];
             
         view()->share('sale.sale_ledger_print',$data);
        $pdf = PDF::loadView('sale.sale_ledger_print', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('sale.sale_ledger_print.pdf');


            //return view('sale.sale_ledger',compact('sales','config'));
      }


      public function get_invoice(Request $request)
    {
             $sale=Sale::with('customer','items','items.department','items.unit','items.size','items.color')->find($request->invoice_id);

             return response()->json($sale, 200);
    }

    public function get_customer_product_invoices(Request $request)
    {
            $product_id=$request->product_id;
            $customer_id=$request->customer_id;
             $sale=sale_stock::with('sale','item','item.department')->where('item_id',$product_id)->wherehas('sale',function($q) use($customer_id){
                 $q->where('customer_id', $customer_id);
             })->get();

             return response()->json($sale, 200);
    }

    public function get_invoice_item(Request $request)
    {
      $stock_id=$request->sale_stock_id;

          
             $sale=sale_stock::with('sale','item','item.department','item.unit','item.size','item.color')->find($stock_id);



             return response()->json($sale, 200);
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {     
      
      $list=$sale->sale_stock_list;
      foreach ($list as $key ) {
        $return=salereturn_ledger::where('sale_stock_id',$key['id'])->first();
        if($return!='')
        return redirect()->back()->withErrors(['error'=>'Delete sale return first, than invoice!']);
      }
     
          

          
         $sale->items()->detach();


            foreach($sale->transections as $trans )
           {
               $trans->delete();
           }


         $sale->delete();

        return redirect(url('sale/create'))->with('success','Invoice Deleted!');
    }

    public function import_sale()
    {
        
        return view('sale.import_sale');
    }

    public function save_import_sale(Request $request)
    {
       
        \Excel::import(new SalesImport,request()->file('sheet'));

        //\Session::put('success', 'Your file is imported successfully in database.');

        return redirect()->back()->with('success','Your file is imported successfully in database.');
           
    
    }
}
