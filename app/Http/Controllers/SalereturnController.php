<?php

namespace App\Http\Controllers;

use App\Models\Salereturn;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\InventoryDepartment;
use App\Models\inventory;
use App\Models\Transection;
use App\Models\Deliverychallan;
use App\Models\salereturn_ledger;
use App\Models\sale_stock;
use App\Models\Configuration;
use PDF;

class SalereturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales=Salereturn::orderBy('doc_no', 'desc')->get();

        return view('sale.return_history',compact('sales'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $doc_no="SR-";
        $num=1;

         $order=Salereturn::select('id','doc_no')->orderBy('doc_no','desc')->first();
         if($order=='')
         {
              $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $order['doc_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }

        $customers=Customer::where('activeness','1')->get();
        
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();
        $sales=Sale::select('id','invoice_no','invoice_date')->orderBy('invoice_no','desc')->get()->whereNull('salereturn');
        $products=inventory::where('department_id','1')->where('activeness','like','active')->get();

        return view('sale.sale_return',compact('products','departments','doc_no','customers','sales'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $chln=Salereturn::where('doc_no',$request->doc_no)->first();
            if($chln!='')
             return redirect()->back()->withErrors(['error'=>'Doc No. already existed!']);
           
        $location_ids=$request->location_ids;

       $items_id=$request->items_id;
       $stocks_id=$request->stocks_id;
       $units=$request->units;
        $qtys=$request->qtys;
        $pack_sizes=$request->p_s;

        $mrps=$request->mrp;
        $tps=$request->tp;
         $business_type=$request->business_type;
        $batch_nos=$request->batch_no;
        $expiry_dates=$request->expiry_date;
        $discount_types=$request->discount_type;
        $discount_factors=$request->discount_factor;
        $taxs=$request->tax;

         
         $active=$request->active;
        if($active=='')
            $active='0';

        

        $challan=new Salereturn;

        $challan->doc_no=$request->doc_no;
        $challan->doc_date=$request->doc_date;
        
        
        $challan->activeness=$active;
        
        $challan->customer_id=$request->customer_id;

        //$challan->challan_id=$request->challan_id;
         //$challan->net_discount=$request->disc;
         // $challan->net_discount_type=$request->net_disc;
        $challan->remarks=$request->remarks;

        $challan->save();
            
            for($i=0;$i<count($items_id);$i++)
            {
         $challan->items()->attach($items_id[$i] , ['sale_stock_id'=>$stocks_id[$i],'unit' => $units[$i] , 'qty' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i] ,'mrp'=>$mrps[$i],'business_type'=>$business_type[$i] ,'batch_no'=>$batch_nos[$i] ,'expiry_date'=>$expiry_dates[$i] ,'discount_type'=>$discount_types[$i],'discount_factor'=>$discount_factors[$i],'tax'=>$taxs[$i] ]);

           }

           foreach ($challan->return_list as $item) {
                   

                
               $rate=$challan->rate($item['item']['id'],$item['id']);
               $amount= $rate * ($item['qty'] * $item['pack_size'] );

                $remarks='Return: '.$item['item']['item_name'].' ('.$rate.'*'.$item['qty'].')';

         $customer_acc=Customer::find($request->customer_id)->account_id;
           $trans=new Transection;

           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Salereturn';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$amount;

           $trans->save();


           }

           

        return redirect('/edit/sale/return/'.$challan['id'])->with('success','Sale return genrated!');
                  return redirect()->back()->with('success','Sale return genrated!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Salereturn  $salereturn
     * @return \Illuminate\Http\Response
     */
    public function show(Salereturn $salereturn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Salereturn  $salereturn
     * @return \Illuminate\Http\Response
     */
    public function edit(Salereturn $return)
    {
        $customers=Customer::where('activeness','1')->get();
        
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();
        $products=inventory::where('department_id','1')->where('activeness','like','active')->get();

        $items=array();
             foreach ($return['items'] as $key ) {
                 
                 $pivot_id=$key['pivot']['id'];
                 $sale_stock_id=$key['pivot']['sale_stock_id'];
                 $invoice_no='';
                    if($sale_stock_id!='')
                 {
                  $s=sale_stock::find($sale_stock_id);
                   if($s!='')
                    $invoice_no=$s->sale->invoice_no;
                }

                 $unit=$key['pivot']['unit'];
                 $qty=$key['pivot']['qty'];
                 $pack_size=$key['pivot']['pack_size'];
                 $total_qty=$qty * $pack_size;
                 $mrp=$key['pivot']['mrp'];
                 $tp=round( (0.85 * $mrp  ),2);
                 $batch_no=$key['pivot']['batch_no'];
                 $expiry_date=$key['pivot']['expiry_date'];
                 $business_type=$key['pivot']['business_type'];
                 $discount_type=$key['pivot']['discount_type'];
                 $discount_factor=$key['pivot']['discount_factor'];

                 $discounted_value=0;

                 if($discount_type=='flat')
                    $discounted_value=$discount_factor;
                elseif($discount_type=='percentage')
                   $discounted_value=round( (($discount_factor/100)*$tp) ,2);

                  $rate=round($tp- $discounted_value,2);
                  $total=round( ($total_qty * $rate),2);
                 $tax=$key['pivot']['tax'];
                  $tax_amount= round( (($tax/100)*$total),2);

                  $net_amount =round( $tax_amount + $total ,2 );

                    $item=array('pivot_id'=>$pivot_id,'sale_stock_id'=>$sale_stock_id,'invoice_no'=>$invoice_no,'item_id'=>$key['id'],'location_id'=>$key['department_id'],'location_text'=>$key['department']['name'],'item_name'=>$key['item_name'],'unit'=>$unit,'qty'=>$qty,'pack_size'=>$pack_size,'mrp'=>$mrp,'tp'=>$tp,'batch_no'=>$batch_no,'business_type'=>$business_type,'expiry_date'=>$expiry_date,'total_qty'=>$total_qty,'discount_type'=>$discount_type,'discount_factor'=>$discount_factor,'discounted_value'=>$discounted_value,'rate'=>$rate,'total'=>$total,'tax'=>$tax,'tax_amount'=>$tax_amount,'net_amount'=>$net_amount);

                 array_push($items, $item);
             } //print_r(json_encode($items));die;
        return view('sale.edit_return',compact('departments','products','return','items','customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Salereturn  $salereturn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $location_ids=$request->location_ids;
        $pivots_id=$request->pivots_id;
       $items_id=$request->items_id;
       $stocks_id=$request->stocks_id;
       $units=$request->units;
        $qtys=$request->qtys;
        $pack_sizes=$request->p_s;
        $business_type=$request->business_type;
        $mrps=$request->mrp;
        $tps=$request->tp;
        $batch_nos=$request->batch_no;
        $expiry_dates=$request->expiry_date;
        $discount_types=$request->discount_type;
        $discount_factors=$request->discount_factor;
        $taxs=$request->tax;
   //print_r(json_encode($challans_id));die;
         
         $active=$request->active;
        if($active=='')
            $active='0';

        

        $challan=Salereturn::find($request->id);

        $challan->doc_no=$request->doc_no;
        $challan->doc_date=$request->doc_date;
        
        
        $challan->activeness=$active;
        
        $challan->customer_id=$request->customer_id;

        //$challan->challan_id=$request->challan_id;
         //$challan->net_discount=$request->disc;
         // $challan->net_discount_type=$request->net_disc;
        $challan->remarks=$request->remarks;

       $challan->save();

       $items=salereturn_ledger::where('return_id',$challan['id'])->whereNotIn('id',$pivots_id)->get();

        foreach ($items as $tr) {
            $tr->delete();
        }

        for ($i=0;$i<count($items_id);$i++)
           {
                 if($pivots_id[$i]!=0)
                 $item=salereturn_ledger::find($pivots_id[$i]);
                  else
                  $item=new salereturn_ledger;
                
                 $item->return_id=$challan['id'];
                $item->sale_stock_id=$stocks_id[$i];
                $item->item_id=$items_id[$i];
                $item->unit=$units[$i];
                $item->qty=$qtys[$i];
                $item->pack_size=$pack_sizes[$i];
                $item->mrp=$mrps[$i];
                $item->batch_no=$batch_nos[$i];
                $item->expiry_date=$expiry_dates[$i];
                $item->business_type=$business_type[$i];
                $item->discount_type=$discount_types[$i];
                $item->discount_factor=$discount_factors[$i];
                $item->tax=$taxs[$i];
                $item->save();
           }

  

                  $transections=$challan->transections;

            $no=0;
           foreach ($challan->return_list as $item) {
                   

                
               $rate=$challan->rate($item['item']['id'],$item['id']);
               $amount= $rate * ($item['qty'] * $item['pack_size'] );

                $remarks='Return: '.$item['item']['item_name'].' ('.$rate.'*'.$item['qty'].')';

         $customer_acc=Customer::find($request->customer_id)->account_id;
           if($no < count($transections))
          $trans=$transections[$no];
          else
           $trans=new Transection;

           $trans->account_voucherable_id=$challan->id;
           $trans->account_voucherable_type='App\Models\Salereturn';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$amount;

           $trans->save();
            $no++;

           }

           for($i=$no; $i < count($transections); $i++ )
           {
               $transections[$i]->delete();
           }

           
                  return redirect()->back()->with('success','Sale return updated!');
    }

    public function return_report(Salereturn $return,$type)
    {

        $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();
           
           
           
        $data = [
            
            'return'=>$return,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        
        ];
        //return view('sale.estimated_invoice',compact('data'));
        if($type=='invoice1')
           {
            view()->share('sale.reports.return1',$data);
        $pdf = PDF::loadView('sale.reports.return1', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream($return['doc_no'].'.pdf');
      }
        elseif($type=='invoice2')
          {
            view()->share('sale.reports.return2',$data);
        $pdf = PDF::loadView('sale.reports.return2', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream($return['doc_no'].'.pdf');
      }
      
        
        
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Salereturn  $salereturn
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salereturn $return)
    {
        


         $return->items()->detach();


            foreach($return->transections as $trans )
           {
               $trans->delete();
           }


         $return->delete();

        return redirect(url('sale/return'))->with('success','Sale return Deleted!');
    }
}
