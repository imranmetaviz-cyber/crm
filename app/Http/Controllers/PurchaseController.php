<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\inventory;
use App\Models\Vendor;
use App\Models\InventoryDepartment;
use Illuminate\Http\Request;
use App\Models\Purchasedemand;
use App\Models\Configuration;
use App\Models\Purchaseorder;
use App\Models\Grn;
use App\Models\Department;
use App\Models\Expense;
use App\Models\Transection;
use App\Models\Stock;
use App\Models\Purchasereturn;
use App\Models\purchase_ledger;
use App\Models\Purchasereturn_ledger;
use App\Models\Employee;
use PDF;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function get_purchase_code(Request $request)
    {
        $type=$request->get('type');

        //$doc_no="PUR-".Date("y")."-";

        //if($is_gst=='false' )
        $m=Date("m");
        $y=Date("y");
        
        if($m < 7)
          { $y1=$y-1 ; $y=$y1.'/'.$y;}
          else
           { $y1=$y+1 ; $y=$y.'/'.$y1;}

          $doc_no="PUR-".$type."-".$y."-";

        $num=1;

         $purchase=Purchase::select('id','doc_no')->where('doc_no','like',$doc_no.'%')->orderBy('doc_no','desc')->first();

         if($purchase=='')
         {
              $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $purchase['doc_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }

          return response()->json($doc_no, 200);

    }

    public function get_demand(Request $request)
    {
        $demand=Purchasedemand::find($request->demand);

        

        //print_r($demand);die;
        $items=array();
        foreach ($demand->items as $key ) {

            $total=$key['pivot']['quantity']*$key['pivot']['pack_size'];

            $color='';$uom='';$size='';
            if($key['color']!='')
            $color=$key['color']['name'];
            if($key['unit']!='')
            $uom=$key['unit']['name'];
            if($key['size']!='')
            $size=$key['size']['name'];
            
            $item=array('location_id'=>$key['department']['id'],'location'=>$key['department']['name'],'item_id'=>$key['id'],'item_name'=>$key['item_name'],'item_color'=>$color,'item_code'=>$key['item_code'],'item_size'=>$size,'item_uom'=>$uom,'qty'=>$key['pivot']['quantity'],'pack_size'=>$key['pivot']['pack_size'],'unit'=>$key['pivot']['unit'],'total_qty'=>$total);
            array_push($items, $item);
        }

        $demand=array('id'=>$demand['id'],'items'=>$items);

        return response()->json($demand, 200);
    }

    public function get_department_demands($department_id)
    {      
        if($department_id==0)
        {
           $all_demands=Purchasedemand::select('id','doc_no','doc_date','department_id')->where('posted','like','posted')->where('is_approved','like','1')->orderBy('created_at','desc')->get(); 
        }
        else
        {
            $all_demands=Purchasedemand::select('id','doc_no','doc_date','department_id')->where('department_id','like',$department_id)->where('posted','like','posted')->where('is_approved','like','1')->orderBy('created_at','desc')->get();
        }
        
         $demands=array();
         foreach ($all_demands as $all ) {
             $order=Purchaseorder::select('id','purchasedemand_id')->where('purchasedemand_id',$all['id'])->first();
             if($order=='')
                array_push($demands,  $all);
         }
        return response()->json($demands, 200);
    }

    public function purchase_order()
    {
        $doc_no="PO-".Date("y")."-";
        $num=1;

         $order=Purchaseorder::select('id','doc_no')->where('doc_no','like',$doc_no.'%')->orderBy('doc_no','desc')->latest()->first();
         if($order=='')
         {
              $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $order['doc_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }

    $all_demands=Purchasedemand::select('id','doc_no','doc_date')->where('posted','like','posted')->where('is_approved','1')->orderBy('created_at','desc')->get();
    
         $demands=array();
         foreach ($all_demands as $all ) {
             $order=Purchaseorder::select('id','purchasedemand_id')->where('purchasedemand_id',$all['id'])->first();
             if($order=='')
                array_push($demands,  $all);
         }

         $vendors=Vendor::where('activeness','like','1')->orderBy('sort_order','asc')->get();
        //$inventories=inventory::with('department','category','color','unit','type','size')->where('activeness','like','active')->get();
        $inventories=[];
    
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();

        //$departs=Department::where('activeness','1')->orderBy('sort_order')->get();

        $expenses=Expense::where('activeness','1')->orderBy('name','asc')->get();

        $employees=Employee::with('designation')->where('activeness','active')->orderBy('name','asc')->get();
        $methods=Configuration::transport_methods();

        return view('purchase.purchase_order',compact('methods','employees','expenses','vendors','inventories','departments','demands','doc_no'));
    }

    public function purchase_order_save(Request $request)
    {
        
        $chln=Purchaseorder::where('doc_no',$request->doc_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Doc No. already existed!');

        $locations=$request->locations;
         $location_ids=$request->location_ids;
        $items_name=$request->items_name;
       $items_id=$request->items_id;
       $units=$request->units;
        $qtys=$request->qtys;
        $pack_sizes=$request->p_s;

        $current_rate=$request->current_rate;

       $pack_rate=$request->pack_rate;
        $disc=$request->disc;
         $tax=$request->tax;
         
         $posted=$request->posted;
        if($posted=='')
            $posted='unpost';

        $order=new Purchaseorder;
        $order->doc_no=$request->doc_no;
        $order->doc_date=$request->doc_date;
        $order->po_type=$request->po_type;
        $order->received_date=$request->rec_date;
        $order->shipped_via=$request->shipped_via;
        $order->fob_point=$request->fob_point;
        $order->payment_type=$request->payment_type;
         $order->with_holding_tax=$request->with_holding_tax;
         $order->gst_tax=$request->gst_tax;
        $order->posted=$posted;
        $order->vendor_id=$request->vendor_id;
        $order->remarks=$request->remarks;

        $order->order_by=$request->order_by;
        $order->approve_by=$request->approved_by;

        $order->purchasedemand_id=$request->demand;

        $order->terms=$request->terms;
        $order->delivery_terms=$request->delivery_terms;
        $order->payment_terms=$request->payment_terms;

        $order->save();
            
            for($i=0;$i<count($items_id);$i++)
            {
         $order->items()->attach($items_id[$i] , ['unit' => $units[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i] ,'current_rate'=>$current_rate[$i] , 'pack_rate'=>$pack_rate[$i] , 'discount'=>$disc[$i] , 'tax'=>$tax[$i] ]);
           }

              $expense_ids=$request->expense_ids;
        $exp_amount=$request->exp_amount;
        
            //print_r(json_encode($expense_ids));die;
            if($expense_ids!='' || $expense_ids!=null)
            {
           for($i=0;$i<count($expense_ids);$i++)
            {
                 $amount=0; 

              if($exp_amount[$i]!='')
                $amount=$exp_amount[$i];

               $order->expenses()->attach($expense_ids[$i] , ['amount' => $amount   ]);


 
           }
           }

                  return redirect()->back()->with('success','Purchase order genrated!');


    }

    public function edit_purchase_order($id)
    {
       

         $order=Purchaseorder::where('doc_no','like',$id)->first();

      $total_net_qty=0; $total_net_amount=0;
           $items=array();
           foreach ($order['items'] as $key ) {
            $color=''; $size='';$uom='';
            if($key['color']!='')
                $color=$key['color']['name'];
            if($key['size']!='')
                $size=$key['size']['name'];
            if($key['uom']!='')
                $uom=$key['uom']['name'];
            //calculation for total , net etc

            $unit=$key['pivot']['unit'];
            $qty=$key['pivot']['quantity'];
            $pack_size=$key['pivot']['pack_size'];

            $total_qty=$qty * $pack_size ;

            $current_rate=$key['pivot']['current_rate'];
            $pack_rate=$key['pivot']['pack_rate'];
            $gross_qty=$qty;
            if($unit=='loose')
                $rate=$current_rate;
                elseif($unit='pack')
                    {
                        if($pack_rate=='')
                            { $rate=$current_rate; $gross_qty=$total_qty; }
                        else
                        $rate=$pack_rate; 
                    }

            $discount=$key['pivot']['discount'];
               if($discount=='')
                $discount=0;
                $discount_amount=$rate * ($discount/100) ;
                $rate=round ($rate- $discount_amount , 3) ;
            $total=  round( $gross_qty * $rate ,3)  ; //take gross qty as qty because when unit loose it is self total qty and when pack we will take pack qty multply with pack rate
            //$total=$total;
            $tax=$key['pivot']['tax'];
            if($tax=='')
                $tax=0;
            $tax_amount=$total * ($tax/100) ;
            $net_total=round( $tax_amount + $total ,3);

            //$net_total=round($net_total,3);
            
             $total_net_qty+=$total_qty; $total_net_amount+=$net_total;

             //end calculation
               $item=array('id'=>$key['id'],
               'name'=>$key['item_name'],
               'code'=>$key['item_code'],
               'color'=>$color,
               'size'=>$size,
               'uom'=>$uom,
               'location_id'=>$key['department']['id'],
               'location_name'=>$key['department']['name'],
               'unit'=>$key['pivot']['unit'],
               'quantity'=>$key['pivot']['quantity'],
               'pack_size'=>$key['pivot']['pack_size'],
               'total_qty'=>$total_qty,
               'current_rate'=>$key['pivot']['current_rate'],
               'pack_rate'=>$key['pivot']['pack_rate'],
               'rate'=>$rate,
               'discount'=>$key['pivot']['discount'],
               'total'=>$total,
               'tax'=>$key['pivot']['tax'],
               'net_total'=>$net_total,
                 );
               array_push($items, $item);
           }

           $demand_department_id='';
           if($order['purchasedemand_id']!='' && $order['purchasedemand_id']!=0)
           { 
            $demand_department_id=Purchasedemand::find($order['purchasedemand_id'])->department_id;
          }

         $order=array('doc_no'=>$order['doc_no'],
                      'id'=>$order['id'],
                      'po_type'=>$order['po_type'],
                      'doc_date'=>$order['doc_date'],
                      'shipped_via'=>$order['shipped_via'],
                      'fob_point'=>$order['fob_point'],
                      'payment_type'=>$order['payment_type'],
                      'purchasedemand_id'=>$order['purchasedemand_id'],
                      'order_by'=>$order['order_by'],
                      'approve_by'=>$order['approve_by'],

                      'ordered_by'=>$order['orderd_by'],
                      'approved_by'=>$order['approved_by'],

                      'demand_department_id'=>$demand_department_id,
                      'received_date'=>$order['received_date'],
                      'gst_tax'=>$order['gst_tax'],
                      'with_holding_tax'=>$order['with_holding_tax'],
                      'posted'=>$order['posted'],
                      'dispatched_status'=>$order['dispatched_status'],
                      'vendor_id'=>$order['vendor_id'],
                      'vendor_name'=>$order['vendor']['name'],
                      'vendor_address'=>$order['vendor']['address'],
                      'vendor_phone'=>$order['vendor']['mobile'],
                      'remarks'=>$order['remarks'],
                      'terms'=>$order['terms'],
                      'payment_terms'=>$order['payment_terms'],
                      'delivery_terms'=>$order['delivery_terms'],
                      'items'=>$items,
                      'total_net_qty'=>$total_net_qty,
                      'total_net_amount'=>$total_net_amount,
                      'expenses'=>$order['expenses'],
                      );
        
            //print_r($order);die;
         $all_demands=Purchasedemand::select('id','doc_no','doc_date')->where('posted','like','posted')->orderBy('created_at','desc')->get();
         $demands=array();
         foreach ($all_demands as $all ) {
            if($all['id']==$order['purchasedemand_id'])
                {array_push($demands,  $all); continue;}
             $let_order=Purchaseorder::select('id','purchasedemand_id')->where('purchasedemand_id',$all['id'])->first();
             if($let_order=='')
                array_push($demands,  $all);
         }

         $vendors=Vendor::where('activeness','like','1')->orderBy('sort_order','asc')->get();
        //$inventories=inventory::with('department','category','color','unit','type','size')->where('activeness','like','active')->get();
        $inventories=[];
    
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();

        //$departs=Department::where('activeness','1')->orderBy('sort_order')->get();

        $expenses=Expense::where('activeness','1')->orderBy('name','asc')->get();
        $employees=Employee::with('designation')->where('activeness','active')->orderBy('name','asc')->get();
        $methods=Configuration::transport_methods();
        
        return view('purchase.edit_purchase_order',compact('employees','methods','expenses','vendors','inventories','departments','demands','order'));
    }

    public function purchase_order_update(Request $request)
    {
        

        $locations=$request->locations;
         $location_ids=$request->location_ids;
        $items_name=$request->items_name;

       $items_id=$request->items_id;
       $units=$request->units;
        $qtys=$request->qtys;
        $pack_sizes=$request->p_s;

        $current_rate=$request->current_rate;

       $pack_rate=$request->pack_rate;
        $disc=$request->disc;
         $tax=$request->tax;
         
         $posted=$request->posted;
        if($posted=='')
            $posted='unpost';

        $order=Purchaseorder::find($request->id);
              //print_r($order);die;
        $order->doc_no=$request->doc_no;
        $order->doc_date=$request->doc_date;
        $order->po_type=$request->po_type;
        $order->received_date=$request->rec_date;
          $order->shipped_via=$request->shipped_via;
        $order->fob_point=$request->fob_point;
        $order->payment_type=$request->payment_type;
        $order->with_holding_tax=$request->with_holding_tax;
        $order->gst_tax=$request->gst_tax;
        $order->posted=$posted;
        $order->vendor_id=$request->vendor_id;
        $order->remarks=$request->remarks;

        $order->purchasedemand_id=$request->demand;

         $order->order_by=$request->order_by;
        $order->approve_by=$request->approved_by;

        $order->terms=$request->terms;
        $order->delivery_terms=$request->delivery_terms;
        $order->payment_terms=$request->payment_terms;

        $order->save();
            

           $rel=array();
            for($i=0;$i<count($items_id);$i++)
            {  
                $pivot=array('unit' => $units[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i] ,'current_rate'=>$current_rate[$i] , 'pack_rate'=>$pack_rate[$i] , 'discount'=>$disc[$i] , 'tax'=>$tax[$i] );

                $let=array( $items_id[$i].'' =>$pivot );

                $rel=$rel+$let;

         //$demand->items()->sync($items_id[$i] , ['unit' => $units[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i] ]);
           }

           $order->items()->sync($rel);

           $expense_ids=$request->expense_ids;
            $exp_amount=$request->exp_amount;

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
               $exp=Expense::find($expense_ids[$i]);
              
                 
           } 
              $order->expenses()->sync($rel1);
           }
           else
           {  
            
            $order->expenses()->detach();

           }


                  return redirect()->back()->with('success','Purchase order updated!');


    }

    public function order_report(Purchaseorder $order)
    {
       
        $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();

        $data = [
            
            'order'=>$order,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        
        ];
        //return view('sale.estimated_invoice',compact('data'));
        
           view()->share('purchase.order_report',$data);
        $pdf = PDF::loadView('purchase.order_report', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('purchase.order_report.pdf');

      //return view('purchase.edit_purchase',compact('vendors','inventories','departments','purchase','grn','expenses'));
    }

    public function order_report1(Purchaseorder $order)
    {
       
        $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $head=Configuration::company_head_office();
        $logo=Configuration::company_logo();
        $phone=Configuration::company_phone();
        $mobile=Configuration::company_mobile();
        $whats_app=Configuration::company_whats_app();
         $fax=Configuration::company_fax();
          $email=Configuration::company_email();
           $tag_line=Configuration::company_tag_line();


        $data = [
            
            'order'=>$order,
            'name'=>$name,
            'head_office'=>$head,
            'address'=>$address,
            'logo'=>$logo,
            'phone'=>$phone,
            'mobile'=>$mobile,
            'whats_app'=>$whats_app,
            'fax'=>$fax,
            'email'=>$email,
            'tag_line'=>$tag_line,
        
        ];
        //return view('sale.estimated_invoice',compact('data'));
        
           view()->share('purchase.order_report1',$data);
        $pdf = PDF::loadView('purchase.order_report1', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('purchase.order_report1.pdf');

      //return view('purchase.edit_purchase',compact('vendors','inventories','departments','purchase','grn','expenses'));
    }

    public function purchase_orders_history()
    {

    $p_orders=Purchaseorder::with('items')->orderBy('created_at','desc')->get();

    $orders=array();

    foreach ($p_orders as $key ) {

        $total_net_qty=0; $total_net_amount=0;
        
        foreach ($key['items'] as $k) {
            
            $unit=$k['pivot']['unit'];
            $qty=$k['pivot']['quantity'];
            $pack_size=$k['pivot']['pack_size'];

            $total_qty=$qty * $pack_size ;

            $current_rate=$k['pivot']['current_rate'];
            $pack_rate=$k['pivot']['pack_rate'];
            
            $gross_qty=$qty;
            if($unit=='loose')
                $rate=$current_rate;
                elseif($unit='pack')
                    {
                        if($pack_rate=='')
                            { $rate=$current_rate; $gross_qty=$total_qty; }
                        else
                        $rate=$pack_rate; 
                    }

            $discount=$k['pivot']['discount'];
               if($discount=='')
                $discount=0;
                $discount_amount=$rate * ($discount/100) ;
                $rate=$rate- $discount_amount ;
            
            $total=  round( $gross_qty * $rate ,3)  ; //take gross qty as qty because when unit loose it is self total qty and when pack we will take pack qty multply with pack rate
            //$total=$total;
            $tax=$k['pivot']['tax'];
            if($tax=='')
                $tax=0;
            $tax_amount=$total * ($tax/100) ;
            $net_total=round( $tax_amount + $total ,3);

            //$net_total=round($net_total,3);
            
             $total_net_qty+=$total_qty; $total_net_amount+=$net_total;
        }

        $demand_doc_no=''; $demand_date='';

        if($key['demand']!='')
        {
            //print_r($key['demand']);die;
           $demand_doc_no=$key['demand']['doc_no'];
           $demand_date=$key['demand']['doc_date']; 
        }

        $order=array('id'=>$key['id'],'doc_no'=>$key['doc_no'],'doc_date'=>$key['doc_date'],'po_type'=>$key['po_type'],'received_date'=>$key['received_date'],'demand_doc_no'=>$demand_doc_no,'demand_date'=>$demand_date,'dispatched_status'=>$key['dispatched_status'],'vendor_name'=>$key['vendor']['name'],'remarks'=>$key['remarks'],'posted'=>$key['posted'],'net_quantity'=>$total_net_qty,'net_amount'=>$total_net_amount);

        array_push($orders, $order);
    }
   
//print_r($demands);die;

    return view('purchase.purchase_order_history',compact('orders'));

    }

    public function purchase_order_delete(Purchaseorder $order)
    {
         $grn=$order->grn;
          
          //print_r($order);die;

            if($grn!='')
             return redirect()->back()->withErrors(['error'=>'Delete GRN first, than PO!']);

           
           

         $order->items()->detach();
          $order->expenses()->detach();

         $order->delete();

        return redirect(url('purchase/order'))->with('success','PO Deleted!');
    }


    public function purchase_demand()
    {
        $doc_no="PD-".Date("y")."-";
        $num=1;

         $demand=Purchasedemand::select('id','doc_no')->where('doc_no','like',$doc_no.'%')->orderBy('doc_no','desc')->latest()->first();
         if($demand=='')
         {
              $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $demand['doc_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }
        
        //print_r($doc_no);die;
       
    



         $items = Inventory::getVariantItems();
            

        $locations=InventoryDepartment::where('status','1')->orderBy('sort_order','asc')->get();
       
        return view('purchase.purchase_demand',compact('locations','items','doc_no'));
    }

    public function purchase_demand_report($id)
    {
        
               //$doc_no=$request->doc_no;

        $demand=Purchasedemand::where('doc_no','like',$id)->first();

        //print_r($demand);die;
        $items=array();
        $net_qty=0; $net_total_qty=0;
        foreach ($demand->items as $key ) {

            $total=$key['pivot']['quantity']*$key['pivot']['pack_size'];

            $net_total_qty+=$total;
            $net_qty+=$key['pivot']['quantity'];

            $color='';$uom='';$size='';
            if($key['color']!='')
            $color=$key['color']['name'];
            if($key['unit']!='')
            $uom=$key['unit']['name'];
            if($key['size']!='')
            $size=$key['size']['name'];

          //$current_stock=$key['current_stock'];
          $current_stock=$key->closing_stock(['to'=>$demand['doc_date']]);
            
            $item=array('location_id'=>$key['department']['id'],'location'=>$key['department']['name'],'item_id'=>$key['id'],'item_name'=>$key['item_name'],'item_color'=>$color,'item_code'=>$key['item_code'],'item_size'=>$size,'item_uom'=>$uom,'qty'=>$key['pivot']['quantity'],'pack_size'=>$key['pivot']['pack_size'],'unit'=>$key['pivot']['unit'],'total_qty'=>$total,'current_stock'=>$current_stock);
            array_push($items, $item);
        }
        

        $demand=array('id'=>$demand['id'], 'doc_no'=>$demand['doc_no'], 'department_id'=>$demand['department_id'] , 'department_name'=>$demand['department']['name'] ,'doc_date'=>$demand['doc_date'] , 'remarks'=>$demand['remarks'] , 'posted'=>$demand['posted'] ,'items'=>$items , 'net_qty'=>$net_qty ,'net_total_qty'=>$net_total_qty);

         $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();
         
                 
           $data = [
            
            'demand'=>$demand,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        
        ];
        //return view('sale.estimated_invoice',compact('data'));
        
           view()->share('purchase.demand_report',$data);
        $pdf = PDF::loadView('purchase.demand_report', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('purchase.demand_report.pdf');

        //return view('purchase.edit_purchase_demand',compact('locations','inventories','departments','demand'));
    }

    public function edit_purchase_demand($id)
    {
        
               //$doc_no=$request->doc_no;

        $demand=Purchasedemand::where('doc_no','like',$id)->first();

        //print_r($demand);die;
        $items=array();
        $net_qty=0; $net_total_qty=0;
        foreach ($demand->items as $key ) {

            $total=$key['pivot']['quantity']*$key['pivot']['pack_size'];

            $net_total_qty+=$total;
            $net_qty+=$key['pivot']['quantity'];

            $color='';$uom='';$size='';
            if($key['color']!='')
            $color=$key['color']['name'];
            if($key['unit']!='')
            $uom=$key['unit']['name'];
            if($key['size']!='')
            $size=$key['size']['name'];
            
            $item=array('location_id'=>$key['department']['id'],'location'=>$key['department']['name'],'item_id'=>$key['id'],'item_name'=>$key['item_name'],'item_color'=>$color,'item_code'=>$key['item_code'],'item_size'=>$size,'item_uom'=>$uom,'qty'=>$key['pivot']['quantity'],'pack_size'=>$key['pivot']['pack_size'],'unit'=>$key['pivot']['unit'],'total_qty'=>$total);
            array_push($items, $item);
        }
        

        $demand=array('id'=>$demand['id'], 'doc_no'=>$demand['doc_no'], 'department_id'=>$demand['department_id'] , 'doc_date'=>$demand['doc_date'] , 'remarks'=>$demand['remarks'] , 'posted'=>$demand['posted'] , 'is_approved'=>$demand['is_approved'] ,'items'=>$items , 'net_qty'=>$net_qty ,'net_total_qty'=>$net_total_qty);
         
        
        //print_r($doc_no);die;
       
    
        $locations=InventoryDepartment::where('status','1')->orderBy('sort_order','asc')->get();

        return view('purchase.edit_purchase_demand',compact('locations','demand'));
    }


    public function purchase_demands_history()
    {

    $p_demands=Purchasedemand::with('items')->orderBy('doc_no','desc')->get();

    $demands=array();

    foreach ($p_demands as $key ) {

        $qty=0;
        $net_amount=0;
        
        foreach ($key['items'] as $k) {
            
            $qty+=$k['pivot']['quantity'] * $k['pivot']['pack_size'];
        }
             $department='';
             if($key['department']!='')
                $department=$key['department']['name'];
        $demand=array('id'=>$key['id'],'doc_no'=>$key['doc_no'],'department'=>$department,'doc_date'=>$key['doc_date'],'remarks'=>$key['remarks'],'posted'=>$key['posted'],'quantity'=>$qty);

        

        array_push($demands, $demand);
    }
   
//print_r($demands);die;

    return view('purchase.purchase_demand_history',compact('demands'));

    }

    public function purchase_demand_save(Request $request)
    {
      $chln=Purchasedemand::where('doc_no',$request->doc_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Doc No. already existed!');

        $units=$request->units;
        $qtys=$request->qtys;
        $locations=$request->location_ids;
       

       $items_id=$request->items_id;
        $pack_sizes=$request->p_s;
         
         $posted=$request->posted;
        if($posted=='')
            $posted='unposted';

        $demand=new Purchasedemand;
        $demand->doc_no=$request->doc_no;
        $demand->doc_date=$request->doc_date;
        $demand->posted=$posted;
        $demand->department_id=$request->department_id;
        $demand->is_approved=$request->is_approved;
        $demand->remarks=$request->remarks;

        $demand->save();
            
            for($i=0;$i<count($items_id);$i++)
            {
         $demand->items()->attach($items_id[$i] , ['unit' => $units[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i] ]);
           }

                  return redirect()->back()->with('success','Purchase demand genrated!');
    }

     public function purchase_demand_update(Request $request)
    {
        $units=$request->units;
        $qtys=$request->qtys;
        $locations=$request->locations;
        $items_name=$request->items_name;

       $items_id=$request->items_id;
        $pack_sizes=$request->p_s;
         
         $posted=$request->posted;
        if($posted=='')
            $posted='unposted';

        $demand=Purchasedemand::find($request->id);
        $demand->doc_no=$request->doc_no;
        $demand->doc_date=$request->doc_date;
        $demand->posted=$posted;
        $demand->department_id=$request->department_id;
        $demand->is_approved=$request->is_approved;
        $demand->remarks=$request->remarks;

        $demand->save();
            $rel=array();
            for($i=0;$i<count($items_id);$i++)
            {  
                $pivot=array('unit' => $units[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i]);

                $let=array( $items_id[$i].'' =>$pivot );

                $rel=$rel+$let;

         //$demand->items()->sync($items_id[$i] , ['unit' => $units[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i] ]);
           }

           $demand->items()->sync($rel);

        //print_r($rel);die;

                  return redirect()->back()->with('success','Purchase demand updated!');
    }

    public function purchase_demand_delete(Purchasedemand $demand)
    {
         $order=$demand->order;
          
          //print_r($order);die;

            if($order!='')
             return redirect()->back()->withErrors(['error'=>'Delete PO first, than Demand!']);

           
           

         $demand->items()->detach();


            


         $demand->delete();

        return redirect(url('purchase/demand'))->with('success','Demand Deleted!');
    }

    public function goods_receiving_note()
    {

        $doc_no="REC-".Date("y")."-";
        $num=1;

         $g_note=Grn::select('id','grn_no')->where('grn_no','like',$doc_no.'%')->orderBy('grn_no','desc')->latest()->first();
         if($g_note=='')
         {
              $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $g_note['grn_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }

         $all_orders=Purchaseorder::select('id','doc_no','doc_date','vendor_id')->where('posted','like','post')->orderBy('created_at','desc')->get();
        // print_r($all_orders);die;
         $orders=array();
         foreach ($all_orders as $all ) {

             $grn=Grn::select('id','grn_no','doc_date')->where('purchaseorder_id',$all['id'])->first();
             if($grn=='')
                array_push($orders,  $all);
         }

         $vendors=Vendor::where('activeness','like','1')->orderBy('sort_order','asc')->get();
        //$inventories=inventory::with('department','category','color','unit','type','size')->where('activeness','like','active')->get();
    
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();

        return view('purchase.goods_receiving_note',compact('vendors','departments','orders','doc_no'));

        
    }

    public function grn_save(Request $request)
    {
       $chln=Grn::where('grn_no',$request->grn_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Doc No. already existed!');

        $locations=$request->locations;
         $location_ids=$request->location_ids;
        $items_name=$request->items_name;

       $items_id=$request->items_id;
       $units=$request->units;
        $qtys=$request->qtys;
        $rec_qty=$request->rec_qty;
        $pack_sizes=$request->p_s;
        $rej_qty=$request->rej_qty;

        $batch_nos=$request->batch_no;
        $mfg_dates=$request->mfg_date;
         $exp_dates=$request->exp_date;
         $is_sampled=$request->is_sampled;
         $sampled=$request->sampled;
          
         
         $posted=$request->posted;
        if($posted=='')
            $posted='unpost';

        $grn=new Grn;

        $grn->grn_no=$request->grn_no;
        $grn->doc_date=$request->doc_date;
      
        $grn->purchaseorder_id=$request->po_id;
       
        $grn->posted=$posted;
        $grn->vendor_id=$request->vendor_id;
        $grn->remarks=$request->remarks;

       


        $grn->save();
            
            for($i=0;$i<count($items_id);$i++)
            {
                $s='';
                if($sampled[$i]==1)
                  $s='/adv';
              $loc=strtoupper( inventory::find($items_id[$i])->department->code );
    
        $doc_no="FP/".$loc.$s."-".Date("y")."/";
        $doc_no1="FP/".$loc.$s."-";
        $num=1;

         $stk=Stock::where('grn_no','like',$doc_no.'%')->orderBy('grn_no','desc')->latest()->first();   //print_r(json_encode($doc_no1));die;

         if($stk=='')
         {
              $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $stk['grn_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
          $app=$rec_qty[$i] - $rej_qty[$i];
         $grn->items()->attach($items_id[$i] , ['grn_no'=>$doc_no,'unit' => $units[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i] ,'rec_quantity'=>$rec_qty[$i]  , 'approved_qty' =>$app, 'rej_quantity'=>$rej_qty[$i] , 'batch_no' => $batch_nos[$i] ,'mfg_date' => $mfg_dates[$i] ,'exp_date' => $exp_dates[$i] , 'is_active'=>'1','is_sampled'=>$sampled[$i] ]);
           }

                  return redirect()->back()->with('success','GRN genrated!');
    }

    public function purchase_grn_delete(Grn $grn)
    {
         $purchase=$grn->purchase;
          
          //print_r($order);die;

            if($purchase!='')
             return redirect()->back()->withErrors(['error'=>'Delete Purchase first, than GRN!']);

           $r=false;
           foreach ($grn->stock_items as $item) {
               
               if(count($item->qa_samplings)>0)
                {  $r=true; continue; }
                else
                  $item->delete();
           }

         //print_r($r);die;
          if($r==true)
          return redirect()->back()->withErrors(['error'=>'Delete sampling entry, than GRN!']);
          else
           $grn->delete();

        return redirect(url('purchase/goods-receiving-note'))->with('success','GRN Deleted!');
    }


     public function get_po(Request $request)
    {
        $order=Purchaseorder::find($request->po_id);

        

        //print_r($demand);die;
        $items=array();
        foreach ($order->items as $key ) {

      $total=$key['pivot']['quantity']*$key['pivot']['pack_size'];

            $color='';$uom='';$size='';
            if($key['color']!='')
            $color=$key['color']['name'];
            if($key['unit']!='')
            $uom=$key['unit']['name'];
            if($key['size']!='')
            $size=$key['size']['name'];
            
            $item=array('location_id'=>$key['department']['id'],'location'=>$key['department']['name'],'item_id'=>$key['id'],'item_name'=>$key['item_name'],'item_color'=>$color,'item_code'=>$key['item_code'],'item_size'=>$size,'item_uom'=>$uom,'unit'=>$key['pivot']['unit'],'qty'=>$key['pivot']['quantity'],'rec_qty'=>$key['pivot']['quantity'],'pack_size'=>$key['pivot']['pack_size'],'rej_qty'=>'0' , 'gross_qty'=> $key['pivot']['quantity'] ,'total_qty'=>$total);

            array_push($items, $item);
        }

        $order=array('id'=>$order['id'],'vendor_id'=>$order['vendor']['id'],'vendor_name'=>$order['vendor']['name'],'items'=>$items);

        return response()->json($order, 200);
    }

    public function purchase_grn_history()
    {

    $p_grns=Grn::with('items')->orderBy('grn_no','desc')->get();

    $grns=array();

    foreach ($p_grns as $key ) {

        $total_net_qty=0; $total_net_gross=0;
        
        foreach ($key['items'] as $k) {
            
            $unit=$k['pivot']['unit'];
            $rec_qty=$k['pivot']['rec_quantity'];
            $rej_qty=$k['pivot']['rej_quantity'];
            $gross_qty=$rec_qty-$rej_qty;
            $pack_size=$k['pivot']['pack_size'];
            
            if($rej_qty=='')
                $rej_qty=0;
            $total_qty=$gross_qty * $pack_size ;

            
            //$net_total=round($net_total,3);
            $total_net_gross += $gross_qty;
             $total_net_qty += $total_qty; 
        }

        $order_doc_no=''; $order_date=''; $vendor_name='';

        if($key['order']!='')
        {
            //print_r($key['demand']);die;
           $order_doc_no=$key['order']['doc_no'];
           $order_date=$key['order']['doc_date']; 
        }
        if($key['vendor']!='')
        {
            //print_r($key['demand']);die;
           $vendor_name=$key['vendor']['name'];
           
        }
         
         $status=1;
         if($key['purchase']=='')
          $status=0;

        $grn=array('id'=>$key['id'],'grn_no'=>$key['grn_no'],'doc_date'=>$key['doc_date'],'order_doc_no'=>$order_doc_no,'order_date'=>$order_date,'vendor_name'=>$vendor_name,'remarks'=>$key['remarks'],'status'=>$status,'posted'=>$key['posted'],'net_gross'=>$total_net_gross,'net_quantity'=>$total_net_qty);

        array_push($grns, $grn);
    }
   
//print_r($demands);die;

    return view('purchase.purchase_grn_history',compact('grns'));

    }

    public function grn_gatepas($grn_no)
    {
        
       $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();
        
        $grn=Grn::where('grn_no','like',$grn_no)->first();
         
                 
           $data = [
            
            'grn'=>$grn,
             'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        
        ];
      
        
           view()->share('purchase.grn_gatepass',$data);
        $pdf = PDF::loadView('purchase.grn_gatepass', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('purchase.grn_gatepass.pdf');

 
    }

    public function edit_purchase_grn($grn_no)
    {

      
         $g_note=Grn::where('grn_no','like',$grn_no)->first();
         
         $t_qty=0;$total_rec_qty=0;$total_rej_qty=0;$total_gross_qty=0;$total_net_qty=0;

           $items=array();

           foreach ($g_note['stock_items'] as $key ) {

            
            $color=''; $size='';$uom='';
            if($key['item']['color']!='')
                $color=$key['item']['color']['name'];
            if($key['item']['size']!='')
                $size=$key['item']['size']['name'];
            if($key['item']['unit']!='')
                $uom=$key['item']['unit']['name'];
            //calculation for total , net etc

            $unit=$key['unit'];
            $qty=$key['quantity'];
            $rec_qty=$key['rec_quantity'];
            $rej_qty=$key['rej_quantity'];
            $pack_size=$key['pack_size'];
            $gross_qty=$rec_qty - $rej_qty ;

            $total_qty=($rec_qty-$rej_qty) * $pack_size ;
             
             $t_qty+=$qty;
             $total_rec_qty+=$rec_qty;
             $total_rej_qty+=$rej_qty;
             $total_gross_qty+=$gross_qty;
            $total_net_qty+=$total_qty;

             //end calculation
               $item=array('id'=>$key['item']['id'],
                'pivot_id'=>$key['id'],
                'grn_no'=>$key['grn_no'],
               'name'=>$key['item']['item_name'],
               'code'=>$key['item']['item_code'],
               'color'=>$color,
               'size'=>$size,
               'uom'=>$uom,
               'location_id'=>$key['item']['department']['id'],
               'location_name'=>$key['item']['department']['name'],
               'unit'=>$key['unit'],
               'quantity'=>$key['quantity'],
               'pack_size'=>$key['pack_size'],
               'total_qty'=>$total_qty,
               'rec_qty'=>$key['rec_quantity'],
               'rej_qty'=>$key['rej_quantity'],
               'gross_qty'=>$gross_qty,
               'batch_no'=>$key['batch_no'],
               'mfg_date'=>$key['mfg_date'],
               'exp_date'=>$key['exp_date'],
               'is_sampled'=>$key['is_sampled'],
                 );
               array_push($items, $item);
           }

           $vendor_name='';

           if($g_note['vendor']!='')
        {
           $vendor_name=$g_note['vendor']['name'];
           
        }
         $grn=array('grn_no'=>$g_note['grn_no'],
                      'id'=>$g_note['id'],
                      
                      'doc_date'=>$g_note['doc_date'],
                      'purchaseorder_id'=>$g_note['purchaseorder_id'],
                    
                      'posted'=>$g_note['posted'],
                      
                      'vendor_id'=>$g_note['vendor_id'],
                      'vendor_name'=>$vendor_name,
                      'remarks'=>$g_note['remarks'],
                        'items'=>$items,
                      'total_qty'=>$t_qty,
                      'total_rec_qty'=>$total_rec_qty,
                      'total_rej_qty'=>$total_rej_qty,
                      'total_gross_qty'=>$total_gross_qty,
                      'total_net_qty'=>$total_net_qty,
                      );
         //print_r($grn);die;
         
            //print_r($order);die;
         //$orders=Purchaseorder::select('id','doc_no','doc_date')->where('id',$grn['purchaseorder_id'])->where('posted','like','post')->orderBy('created_at','desc')->get();

         $all_orders=Purchaseorder::select('id','doc_no','doc_date','vendor_id')->where('posted','like','post')->orderBy('created_at','desc')->get();
         $orders=array();
         foreach ($all_orders as $all ) {
            if($all['id']==$grn['purchaseorder_id'])
                {array_push($orders,  $all); continue;}
             $let_grn=Grn::select('id','purchaseorder_id')->where('purchaseorder_id',$all['id'])->first();
             if($let_grn=='')
                array_push($orders,  $all);
         }


         $vendors=Vendor::where('activeness','like','1')->orderBy('sort_order','asc')->get();
        //$inventories=inventory::with('department','category','color','unit','type','size')->where('activeness','like','active')->get();
    
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();

        return view('purchase.edit_grn',compact('vendors','departments','orders','grn'));

        
    }

    public function update_purchase_grn(Request $request)
    {
        $pivot_ids=$request->pivot_ids;
        $locations=$request->locations;
         $location_ids=$request->location_ids;
        $items_name=$request->items_name;

       $items_id=$request->items_id;
       $units=$request->units;
        $qtys=$request->qtys;
        $rec_qty=$request->rec_qty;
        $pack_sizes=$request->p_s;
        $rej_qty=$request->rej_qty;
            $batch_nos=$request->batch_no;
        $mfg_dates=$request->mfg_date;
         $exp_dates=$request->exp_date;
        $is_sampled=$request->is_sampled;
         $sampled=$request->sampled;
         
         $posted=$request->posted;
        if($posted=='')
            $posted='unpost';

        $grn=Grn::find($request->id);

        
            

        $grn->grn_no=$request->grn_no;
        $grn->doc_date=$request->doc_date;
        $grn->purchaseorder_id=$request->po_id;
        $grn->posted=$posted;
        $grn->vendor_id=$request->vendor_id;
        $grn->remarks=$request->remarks; 
        $grn->save();
            
            //if($grn['purchase']!='')
           //return redirect()->back()->withErrors(['error'=>'Grn can not be updated, first delete purchase!']);
          
          // foreach ($grn->stock_items as $smp) {
            
          // }

        $items=Stock::where('grn_id',$grn['id'])->whereNotIn('id',$pivot_ids)->get();
           
           $b=false;
        foreach ($items as $tr) {
          if(count($tr->qa_samplings)>0)
               { $b=true; continue;}
             if($tr->purchase!='')
               { $b=true; continue;}
          $tr->delete();
        }

           for ($i=0;$i<count($items_id);$i++)
           {
                 if($pivot_ids[$i]!=0)
                 $item=Stock::find($pivot_ids[$i]);
                  else
                  {
                    $item=new Stock;
                     $item->is_active=1;
                   }

                   if($pivot_ids[$i]==0 || ($sampled[$i]=='1'&&$item['is_sampled']==0) || ($sampled[$i]=='0'&&$item['is_sampled']==1) )
                   {
                    $s='';
                if($sampled[$i]==1)
                  $s='/adv';
                    $loc=strtoupper( inventory::find($items_id[$i])->department->code );
    
                   $doc_no="FP/".$loc.$s."-".Date("y")."/";
                   $doc_no1="FP/".$loc.$s."-";
                    $num=1;

                     $stk=Stock::where('grn_no','like',$doc_no.'%')->orderBy('grn_no','desc')->latest()->first();   //print_r(json_encode($stk));die;
                     if($stk=='')
                     {
                          $let=sprintf('%03d', $num);
                          $doc_no=$doc_no. $let;
                     }
                     else
                     {
                        $let=explode($doc_no , $stk['grn_no']);
                        $num=intval($let[1]) + 1;
                        $let=sprintf('%03d', $num);
                          $doc_no=$doc_no. $let;
                     }

                     $item->grn_no=$doc_no;
                    
                  }

                $item->grn_id=$grn['id'];
                $item->item_id=$items_id[$i];
                $item->unit=$units[$i];
                $item->quantity=$qtys[$i];
                $item->pack_size=$pack_sizes[$i];
                $item->rec_quantity=$rec_qty[$i];

                $app=$rec_qty[$i] - $rej_qty[$i];
                $item->approved_qty=$app;

                $item->rej_quantity=$rej_qty[$i];
                $item->batch_no=$batch_nos[$i];

                $item->mfg_date=$mfg_dates[$i];
                $item->exp_date=$exp_dates[$i];
                $item->is_sampled=$sampled[$i];

                $item->save();
           }
          
          if($b==true)
            return redirect()->back()->with('info','GRN Updated but items can not be deleted due to purchase or sampling entry!');

          return redirect()->back()->with('success','GRN Updated!');
    }


    public function add_purchase()
    {
        $m=Date("m");
        $y=Date("y");
        
        if($m < 7)
          { $y1=$y-1 ; $y=$y1.'/'.$y;}
          else
           { $y1=$y+1 ; $y=$y.'/'.$y1;}

        //print_r($m.' '.$y);die;

        $doc_no="PUR-WHT-".$y."-";
        $num=1;

         $purchase=Purchase::select('id','doc_no')->where('doc_no','like',$doc_no.'%')->orderBy('doc_no','desc')->first();
         if($purchase=='')
         {
              $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $purchase['doc_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }

         $grns=Grn::select('id','grn_no','doc_date','vendor_id')->where('posted','like','post')->orderBy('created_at','desc')->doesntHave('purchase')->get();
         //with('items','purchase')->
         //print_r(json_encode($all_grns));die;
        
         
//die;
         $vendors=Vendor::where('activeness','like','1')->orderBy('sort_order','asc')->get();
        //$inventories=inventory::with('department','category','color','unit','type','size')->where('activeness','like','active')->get();
    
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();
        $expenses=Expense::where('activeness','1')->orderBy('name','asc')->get();

      return view('purchase.purchase',compact('vendors','departments','grns','doc_no','expenses'));
    }

    public function get_grn(Request $request)
    {
        $grn=Grn::find($request->grn_id);

       

        //print_r($demand);die;
        $items=array();
        foreach ($grn->items as $key ) {
         //print_r(json_encode( $key ));die;
         $qty=$key['pivot']['rec_quantity'];

  
         
         $p_s=$key['pivot']['pack_size'];
         
             $total_qty=$qty * $p_s;
               
               if(isset($grn->order->items))
               $let=$grn->order->items->where('id',$key['id'])->first();
             // print_r(json_encode($let[0]['pivot']));die;
             $current_rate='';
             $pack_rate='';
             $discount=0;
             $tax=0;
               
              if(isset($let['pivot']['current_rate']))
                {
                    if($let['pivot']['current_rate']!='')
                    $current_rate=$let['pivot']['current_rate'];
               }
            if(isset($let['pivot']['pack_rate']))
               {
                if($let['pivot']['pack_rate']!='')
                $pack_rate=$let['pivot']['pack_rate'];
               }
            if(isset($let['pivot']['discount']))
                $discount=$let['pivot']['discount'];
            if(isset($let['pivot']['tax']))
                $tax=$let['pivot']['tax'];
              
              //die;
            $color='';$uom='';$size='';
            if($key['color']!='')
            $color=$key['color']['name'];
            if($key['unit']!='')
            $uom=$key['unit']['name'];
            if($key['size']!='')
            $size=$key['size']['name'];

             $unit=$key['pivot']['unit'];
             $rate=0;
            if($current_rate!='')
                $rate=$current_rate;
                //elseif($unit=='pack')
            elseif($pack_rate!='')
                    $rate=$pack_rate / $p_s;

            
               if($discount=='')
                $discount=0;
                $discount_amount=$rate * ($discount/100) ;
                $rate=round ( ($rate- $discount_amount) , 2) ;

            $total=  round( $total_qty * $rate ,2)  ;
            //$total=$total;
           
            if($tax=='')
                $tax=0;
            $tax_amount=$total * ($tax/100) ;
            $net_total=round( $tax_amount + $total ,2);
            
            
            $item=array('location_id'=>$key['department']['id'],'location'=>$key['department']['name'],'item_id'=>$key['id'],'item_name'=>$key['item_name'],'item_color'=>$color,'item_code'=>$key['item_code'],'item_size'=>$size,'item_uom'=>$uom,'unit'=>$key['pivot']['unit'],'qty'=>$qty,'pack_size'=>$key['pivot']['pack_size'],'total_qty'=>$total_qty,'current_rate'=>$current_rate,'pack_rate'=>$pack_rate,'discount'=>$discount,'discount_amount'=>$discount_amount ,'rate'=>$rate ,'total'=>$total, 'tax'=>$tax, 'net_total'=>$net_total ,'grn_no'=>$key['pivot']['grn_no'],'stock_id'=>$key['pivot']['id'] );
            array_push($items, $item);
        }
//die;

        $vendor_id='';
        $vendor_name='';
             if($grn['vendor']!='')
                $vendor_id=$grn['vendor']['id'];
              if($grn['vendor']!='')
                $vendor_name=$grn['vendor']['name'];

              $expenses=array();
              if(isset($grn->order))
               $expenses=$grn->order->expenses;
        $grn=array('id'=>$grn['id'],'vendor_id'=>$vendor_id,'vendor_name'=>$vendor_name,'items'=>$items,'expenses'=>$expenses);

        return response()->json($grn, 200);
    }

    public function purchase_save(Request $request)
    {

       $chln=Purchase::where('doc_no',$request->purchase_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Doc No. already existed!');
      //items
        //$locations=$request->locations;
         $location_ids=$request->location_ids;
        //$items_name=$request->items_name;

       $items_id=$request->items_id;
       $stocks=$request->items_stock;

       $units=$request->units;
        $qtys=$request->qtys;
      
        $pack_sizes=$request->p_s;
      
        $current_rate=$request->current_rate;

       $pack_rate=$request->pack_rate;
        $disc=$request->disc;
         $tax=$request->tax;
         //expense
        $expense_ids=$request->expense_ids;
        $exp_debit=$request->exp_debit;
    

         
         $posted=$request->posted;
        if($posted=='')
            $posted='0';

        //   $is_gst=$request->is_gst;
        // if($is_gst=='')
        //     $is_gst='0';

        $purchase=new Purchase;

        $purchase->doc_no=$request->purchase_no;
        $purchase->doc_date=$request->doc_date;
      
        $purchase->grn_id=$request->grn_id;
        $purchase->net_discount=$request->net_discount;
        $purchase->gst_tax=$request->gst;
        $purchase->net_tax=$request->net_tax;
       
        $purchase->posted=$posted;
        // $purchase->is_gst=$is_gst;
        $purchase->vendor_id=$request->vendor_id;
        $purchase->type=$request->type;
        $purchase->remarks=$request->remarks;

        $purchase->save();
            
            for($i=0;$i<count($items_id);$i++)
            {
           $purchase->items()->attach($items_id[$i] , ['stock_id'=>$stocks[$i],'unit' => $units[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i]  ,'current_rate'=>$current_rate[$i] , 'pack_rate'=>$pack_rate[$i] , 'discount'=>$disc[$i] , 'tax'=>$tax[$i] ]);
               

           }


            //print_r(json_encode($expense_ids));die;
            

           foreach ($purchase->item_list as $item ) {
                    

                $rate=$purchase->rate($item['item']['id'],$item['id']);
                $amount=$rate * $item->total_qty();
                
                $remarks=$item['item']['item_name'].' ('.$rate.'*'.$item['quantity'].')';


           if($request->vendor_id!='')
           {
               $customer_acc=Vendor::find($request->vendor_id)->account_id;
               $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$amount;
           $trans->save();

           }

           $depart=InventoryDepartment::find($item['item']['department_id'])->account_id;
           
           $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id=$depart;
          // $trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$amount;
           $trans->credit=0;
           $trans->save();


                     }

                     //gst transections
                     $gst=$purchase->gst_amount(); 

                     if($request->vendor_id!='' && $gst > 0)
               {

                
               $customer_acc=Vendor::find($request->vendor_id)->account_id;
              
              $remarks='GST against : '.$purchase['vendor']['name'].', '.$purchase->doc_no;
              
                   $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$gst;
           $trans->save();

           $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id='680';
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$gst;
           $trans->credit=0;
           $trans->save();
           }

                   
                   //withholding transections
                     $tax=$purchase->with_hold_tax_amount(); 

                     if($request->vendor_id!='' && $tax > 0)
               {

                
               $customer_acc=Vendor::find($request->vendor_id)->account_id;
              
              $remarks='Tax Deduction against : '.$purchase['vendor']['name'].', '.$purchase->doc_no;
              
                   $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$tax;
           $trans->credit=0;
           $trans->save();

           $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id='274';
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$tax;
           $trans->save();
           }

             //start expense

           if($expense_ids!='' || $expense_ids!=null)
            {
           for($i=0;$i<count($expense_ids);$i++)
            {
                 $debit=0;

              if($exp_debit[$i]!='')
                $debit=$exp_debit[$i];

            

               $purchase->expenses()->attach($expense_ids[$i] , ['amount' => $debit   ]);

               $exp=Expense::find($expense_ids[$i]);

               $trans=new Transection;

              $trans->account_voucherable_id=$purchase->id;
              $trans->account_voucherable_type='App\Models\Purchase';
             $trans->account_id=$exp['account_id'];
             //$trans->corporate_id=$exp['id'];
             $trans->remarks=$exp['name'].' Ref: '.$purchase->doc_no;
              $trans->debit=$debit;
               $trans->credit=0;
               $trans->save();

               if($request->vendor_id!='')
               {
                    $customer_acc=Vendor::find($request->vendor_id)->account_id;
                    $trans=new Transection;

              $trans->account_voucherable_id=$purchase->id;
              $trans->account_voucherable_type='App\Models\Purchase';
             $trans->account_id=$customer_acc;
             //$trans->corporate_id=$exp['id'];
             $trans->remarks=$exp['name'].' Ref: '.$purchase->doc_no;
              $trans->debit=0;
               $trans->credit=$debit;
               $trans->save();
               }
 
           }
           }
            

                  return redirect()->back()->with('success','Purchase genrated!');
    }

    public function purchase_delete(Purchase $purchase)
    {
         $return=$purchase->purchasereturn;
          
          //print_r($order);die;

            if($return!='')
             return redirect()->back()->withErrors(['error'=>'Delete Purchase return first, than Purchase!']);

          
           
             $purchase->items()->detach();
             $purchase->expenses()->detach();
              
              foreach ($purchase->transections as $key ) {
                  
                  $key->delete();
              }

         //print_r($r);die;
          
           $purchase->delete();

        return redirect(url('purchase'))->with('success','Purchase Deleted!');
    }

    public function purchase_history()
    {

    $purchases=Purchase::orderBy('doc_no','desc')->get();

    return view('purchase.purchase_history',compact('purchases'));

    }

   public function purchase_report(Purchase $purchase)
    {
       
          $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();

        $data = [
            
            'purchase'=>$purchase,
             'name'=>$name,
            'address'=>$address,
              'logo'=>$logo,
        ];
        //return view('sale.estimated_invoice',compact('data'));
        
           view()->share('purchase.purchase_report',$data);
        $pdf = PDF::loadView('purchase.purchase_report', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('purchase.purchase_report.pdf');

      //return view('purchase.edit_purchase',compact('vendors','inventories','departments','purchase','grn','expenses'));
    }


     public function edit_purchase(Purchase $purchase)
    {
       
        $vendors=Vendor::select('id','name')->where('activeness','like','1')->orderBy('sort_order','asc')->get();
        //$inventories=inventory::with('department','category','color','unit','type','size')->where('activeness','like','active')->get();
    
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();
        $expenses=Expense::where('activeness','1')->orderBy('name','asc')->get();

        $grn=Grn::select('id','grn_no','doc_date','vendor_id')->find($purchase['grn_id']);

      return view('purchase.edit_purchase',compact('vendors','departments','purchase','grn','expenses'));
    }

     public function purchase_update(Request $request)
    {
         
      //items
         $location_ids=$request->location_ids;
        $pivot_ids=$request->pivot_ids;

       $items_id=$request->items_id;
       $stocks=$request->items_stock;
       $units=$request->units;
        $qtys=$request->qtys;
      
        $pack_sizes=$request->p_s;
      
        $current_rate=$request->current_rate;

       $pack_rate=$request->pack_rate;
        $disc=$request->disc;
         $tax=$request->tax;
         //expense
        $expense_ids=$request->expense_ids;
        $exp_debit=$request->exp_debit;
  
           
         
         $posted=$request->posted;
        if($posted=='')
            $posted='0';

          $is_gst=$request->is_gst;
        if($is_gst=='')
            $is_gst='0';


        $purchase=Purchase::find($request->id);

        $purchase->doc_no=$request->purchase_no;
        $purchase->doc_date=$request->doc_date;
      
        $purchase->grn_id=$request->grn_id;
        $purchase->net_discount=$request->net_discount;
        $purchase->gst_tax=$request->gst;
        $purchase->net_tax=$request->net_tax;
       
        $purchase->posted=$posted;
        //$purchase->is_gst=$is_gst;
        $purchase->type=$request->type;
        $purchase->vendor_id=$request->vendor_id;
        $purchase->remarks=$request->remarks;

        $purchase->save();

        $items=purchase_ledger::where('purchase_id',$purchase['id'])->whereNotIn('id',$pivot_ids)->get();

        foreach ($items as $tr) {
                $tr->delete();
        }

           for ($i=0;$i<count($items_id);$i++)
           {
                 if($pivot_ids[$i]!=0)
                 $item=purchase_ledger::find($pivot_ids[$i]);
                  else
                  $item=new purchase_ledger;

                $item->purchase_id=$purchase['id'];
                $item->item_id=$items_id[$i];
                $item->stock_id=$stocks[$i];
                $item->unit=$units[$i];
                $item->quantity=$qtys[$i];
                $item->pack_size=$pack_sizes[$i];
                $item->current_rate=$current_rate[$i];
                $item->pack_rate=$pack_rate[$i];
                $item->discount=$disc[$i];
                
                $item->tax=$tax[$i];
                $item->save();
           }

           $transections=$purchase->transections;
            $no=0;
     //print_r(json_encode(count( $transections)));die;
           

            
            
           foreach ($purchase->item_list as $item) {

               $rate=$purchase->rate($item['item']['id'],$item['id']);
                $amount=$rate * $item->total_qty();
                
                $remarks=$item['item']['item_name'].' ('.$rate.'*'.$item['quantity'].')';

                if($request->vendor_id!='')
               {
               $customer_acc=Vendor::find($request->vendor_id)->account_id;

              
                  if($no < count($transections))
                 { $trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$amount;
           $trans->save();
               
           }
           


           $depart=InventoryDepartment::find($item['item']['department_id'])->account_id;
          
              if($no < count($transections))
          { $trans=$transections[$no]; $no++; }
          else
           $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id=$depart;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$amount;
           $trans->credit=0;
           $trans->save();
          
          
           }
            //gst transection

           $gst=$purchase->gst_amount();
           if($request->vendor_id!='' && $gst > 0 )
               {
               $customer_acc=Vendor::find($request->vendor_id)->account_id;

              $remarks='GST against : '.$purchase['vendor']['name'].', '.$purchase->doc_no;
              

                  if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$gst;
           $trans->save();
               

               if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id='680';
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$gst;
           $trans->credit=0;
           $trans->save();
               

           }

           //withholding transection
          $tax=$purchase->with_hold_tax_amount();
           if($request->vendor_id!='' && $tax > 0 )
               {
               $customer_acc=Vendor::find($request->vendor_id)->account_id;

              $remarks='Tax Deduction against : '.$purchase['vendor']['name'].', '.$purchase->doc_no;
              

                  if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$tax;
           $trans->credit=0;
           $trans->save();
               

               if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchase';
           $trans->account_id='274';
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$tax;
           $trans->save();
               

           }
//print_r($no);

           $rel1=array();
            if($expense_ids!='' || $expense_ids!=null)
            {
           for($i=0;$i<count($expense_ids);$i++)
            {
                 $debit=0; 

              if($exp_debit[$i]!='')
                $debit=$exp_debit[$i];

             

              $pivot1=array('amount' => $debit  );

                $let1=array( $expense_ids[$i].'' => $pivot1 );

                $rel1=$rel1+$let1;

               $exp=Expense::find($expense_ids[$i]);

              
                  if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;


              $trans->account_voucherable_id=$purchase->id;
              $trans->account_voucherable_type='App\Models\Purchase';
             $trans->account_id=$exp['account_id'];
             //$trans->corporate_id=$exp['id'];
             $trans->remarks=$exp['name'].' Ref: '.$purchase->doc_no;
              $trans->debit=$debit;
               $trans->credit=0;
               $trans->save();
               

               if($request->vendor_id!='')
               {
                    $customer_acc=Vendor::find($request->vendor_id)->account_id;
                    if($no < count($transections))
                    {$trans=$transections[$no]; $no++;}
                     else
                    $trans=new Transection;

              $trans->account_voucherable_id=$purchase->id;
              $trans->account_voucherable_type='App\Models\Purchase';
             $trans->account_id=$customer_acc;
             //$trans->corporate_id=$exp['id'];
             $trans->remarks=$exp['name'].' Ref: '.$purchase->doc_no;
              $trans->debit=0;
               $trans->credit=$debit;
               $trans->save();
               
               }
           } 
           
              $purchase->expenses()->sync($rel1);
           }
           else
           {  
            
            $purchase->expenses()->detach();

           }
             //print_r(count($transections));print_r($no);die; 
           for($k=$no; $k < count($transections); $k++ )
           {
            
              //print_r($i); 
               $transections[$k]->delete();
           }
//die;

        return redirect()->back()->with('success','Purchase updated!');
    }

    public function get_purchase(Request $request)
    {
        $purchase=Purchase::with('vendor','item_list','item_list.stock','item_list.item.department','item_list.item.size','item_list.item.color','item_list.item.unit')->find($request->purchase_id);

             return response()->json($purchase, 200);
      }

    public function add_purchase_return()
    {
        $doc_no="PR-".Date("y")."-";
        $num=1;

         $purchase=Purchasereturn::select('id','doc_no')->where('doc_no','like',$doc_no.'%')->orderBy('doc_no','desc')->first();
         if($purchase=='')
         {
              $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $purchase['doc_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%05d', $num);
              $doc_no=$doc_no. $let;
         }

         $purchases=Purchase::select('id','doc_no','doc_date','vendor_id')->where('posted','like','1')->orderBy('created_at','desc')->doesntHave('purchasereturn')->get();
         //->whereNull('purchasereturn');
         //print_r(json_encode($all_grns));die;
         // $grns=array();
         // foreach ($purchases as $all ) {
         //    $grn=Purchasereturn::select('id','purchase_id')->where('purchase_id',$all['id'])->first();
         //      if($grn=='')
         //        array_push($grns,  $all);
         // }

         $vendors=Vendor::where('activeness','like','1')->orderBy('sort_order','asc')->get();
        //$inventories=inventory::with('department','category','color','unit','type','size')->where('activeness','like','active')->get();
    
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();
        $expenses=Expense::where('activeness','1')->orderBy('name','asc')->get();

      return view('purchase.purchase_return',compact('vendors','departments','purchases','doc_no','expenses'));
    }



    public function purchase_return_save(Request $request)
    {
       $chln=Purchasereturn::where('doc_no',$request->purchase_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Doc No. already existed!');

      //items
         $location_ids=$request->location_ids;

       $items_id=$request->items_id;
       $stocks=$request->items_stock;
       $units=$request->units;
       $p_qtys=$request->p_qtys;
        $qtys=$request->qtys;
      
        $pack_sizes=$request->p_s;
        $current_rate=$request->current_rate;
       $pack_rate=$request->pack_rate;
        $disc=$request->disc;
         $tax=$request->tax;
         //expense
        // $expense_ids=$request->expense_ids;
        // $exp_debit=$request->exp_debit;
        //  $exp_credit=$request->exp_credit;

         
         $posted=$request->posted;
        if($posted=='')
            $posted='0';


        $purchase=new Purchasereturn;

        $purchase->doc_no=$request->purchase_no;
        $purchase->doc_date=$request->doc_date;
      
        $purchase->purchase_id=$request->purchase_id;
        //$purchase->net_discount=$request->net_discount;
        $purchase->gst_tax=$request->gst;
        $purchase->net_tax=$request->net_tax;
       
        $purchase->posted=$posted;
        $purchase->vendor_id=$request->vendor_id;
        $purchase->remarks=$request->remarks;

        $purchase->save();
            
            for($i=0;$i<count($items_id);$i++)
            {
           $purchase->items()->attach($items_id[$i] , ['purchase_stock_id'=>$stocks[$i] ,'unit' => $units[$i],'p_qty' => $p_qtys[$i] , 'quantity' => $qtys[$i] ,'pack_size'=>$pack_sizes[$i]  ,'current_rate'=>$current_rate[$i] , 'pack_rate'=>$pack_rate[$i] , 'discount'=>$disc[$i] , 'tax'=>$tax[$i] ]);
               

           }
            //print_r(json_encode($expense_ids));die;
           //  if($expense_ids!='' || $expense_ids!=null)
           //  {
           // for($i=0;$i<count($expense_ids);$i++)
           //  {
           //       $debit=0; $credit=0;

           //    if($exp_debit[$i]!='')
           //      $debit=$exp_debit[$i];

           //    if($exp_credit[$i]!='')
           //      $credit=$exp_credit[$i];

           //     $purchase->expenses()->attach($expense_ids[$i] , ['debit' => $debit , 'credit' => $credit  ]);

           //     $exp=Expense::find($expense_ids[$i]);

           //     $trans=new Transection;

           //    $trans->account_voucherable_id=$purchase->id;
           //    $trans->account_voucherable_type='App\Models\Purchase';
           //   $trans->account_id=$exp['account_id'];
           //   $trans->corporate_id=$exp['id'];
           //   $trans->remarks='Ref: Purchase '.$purchase->doc_no;
           //    $trans->debit=$debit;
           //     $trans->credit=$credit;
           //     $trans->save();
 
           // }
           // }

           foreach ($purchase->item_list as $item ) {
                    

                $rate=$purchase->rate($item['item']['id'],$item['id']);
                $amount=$rate * $item['quantity'];
                $remarks=$item['item']['item_name'].' ('.$rate.'*'.$item['quantity'].')';


           if($request->vendor_id!='')
           {
               $customer_acc=Vendor::find($request->vendor_id)->account_id;
               $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$amount;
           $trans->credit=0;
           $trans->save();

           }

           $depart=InventoryDepartment::find($item['item']['department_id'])->account_id;
           
           $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id=$depart;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$amount;
           $trans->save();


                     }

                     //gst transections
                     $gst=$purchase->gst_tax_amount(); 

                     if($request->vendor_id!='' && $gst > 0)
               {

                
               $customer_acc=Vendor::find($request->vendor_id)->account_id;
              
              $remarks='GST against : '.$purchase['vendor']['name'].', '.$purchase->doc_no;
              
                   $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$gst;
           $trans->credit=0;
           $trans->save();

           $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id='680';
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$gst;
           $trans->save();
           }

                  

           $tax=$purchase->with_hold_tax_amount(); 

                     if($request->vendor_id!='' && $tax > 0)
               {

                
               $customer_acc=Vendor::find($request->vendor_id)->account_id;
              
              $remarks='Tax Deduction against : '.$purchase['vendor']['name'].', '.$purchase->doc_no;
              
                   $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$tax;
           $trans->save();

           $trans=new Transection;
           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id='274';
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$tax;
           $trans->credit=0;
           $trans->save();
           }




            

                  return redirect()->back()->with('success','Purchase Returned!');
    }

     public function purchase_return_delete(Purchasereturn $return)
    {
         

          
           
             $return->items()->detach();
             
              
              foreach ($return->transections as $key ) {
                  
                  $key->delete();
              }

         //print_r($r);die;
          
           $return->delete();

        return redirect(url('purchase/return'))->with('success','Purchase return deleted!');
    }


    public function purchase_return_history()
    {

    $returns=Purchasereturn::with('items')->orderBy('doc_no','desc')->get();

    

    return view('purchase.purchase_return_history',compact('returns'));

    }

    public function edit_purchase_return(Purchasereturn $return)
    {
       
        $vendors=Vendor::where('activeness','like','1')->orderBy('sort_order','asc')->get();
        //$inventories=inventory::with('department','category','color','unit','type','size')->where('activeness','like','active')->get();
    
        $departments=InventoryDepartment::where('activeness','like','active')->orderBy('sort_order','asc')->get();
       // $expenses=Expense::where('activeness','1')->orderBy('name','asc')->get();

        $purchase=Purchase::select('id','doc_no','doc_date','vendor_id')->find($return['purchase_id']);

      return view('purchase.edit_purchase_return',compact('vendors','departments','return','purchase'));
    }

    public function purchase_return_update(Request $request)
    {

         
      //items
        //$locations=$request->locations;
         $location_ids=$request->location_ids;
        $pivot_ids=$request->pivot_ids;

       $items_id=$request->items_id;
       $stocks=$request->items_stock;
       $units=$request->units;
       $p_qtys=$request->p_qtys;
        $qtys=$request->qtys;
      
        $pack_sizes=$request->p_s;
      
        $current_rate=$request->current_rate;

       $pack_rate=$request->pack_rate;
        $disc=$request->disc;
         $tax=$request->tax;
         //expense
        $expense_ids=$request->expense_ids;
        $exp_debit=$request->exp_debit;
         $exp_credit=$request->exp_credit;

         
         $posted=$request->posted;
        if($posted=='')
            $posted='0';

        $purchase=Purchasereturn::find($request->id);
        $purchase->doc_no=$request->purchase_no;
        $purchase->doc_date=$request->doc_date;
        $purchase->purchase_id=$request->purchase_id;
        //$purchase->net_discount=$request->net_discount;
        $purchase->gst_tax=$request->gst;
        $purchase->net_tax=$request->net_tax;
        $purchase->posted=$posted;
        $purchase->vendor_id=$request->vendor_id;
        $purchase->remarks=$request->remarks;
        $purchase->save();

        $items=Purchasereturn_ledger::where('return_id',$purchase['id'])->whereNotIn('id',$pivot_ids)->get();

        foreach ($items as $tr) {
                $tr->delete();
        }

           for ($i=0;$i<count($items_id);$i++)
           {
                 if($pivot_ids[$i]!=0)
                 $item=Purchasereturn_ledger::find($pivot_ids[$i]);
                  else
                  $item=new Purchasereturn_ledger;

                $item->return_id=$purchase['id'];
                $item->item_id=$items_id[$i];
                $item->purchase_stock_id=$stocks[$i];
                $item->unit=$units[$i];
                $item->p_qty=$p_qtys[$i];
                $item->quantity=$qtys[$i];
                $item->pack_size=$pack_sizes[$i];
                $item->current_rate=$current_rate[$i];
                $item->pack_rate=$pack_rate[$i];
                $item->discount=$disc[$i];
                
                $item->tax=$tax[$i];
                $item->save();
           }

           $transections=$purchase->transections;
            $no=0;

            
            foreach ($purchase->item_list as $item ) {
                    

                $rate=$purchase->rate($item['item']['id'],$item['id']);
                $amount=$rate * $item['quantity'];
                
                $remarks=$item['item']['item_name'].' ('.$rate.'*'.$item['quantity'].')';


           if($request->vendor_id!='')
           {
               $customer_acc=Vendor::find($request->vendor_id)->account_id;

              if($no < count($transections))
                  $trans=$transections[$no];
                   else
                   $trans=new Transection;


           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id=$customer_acc;
          // $trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$amount;
           $trans->credit=0;
           $trans->save();
            $no++;
           } 
                       
           

           $depart=InventoryDepartment::find($item['item']['department_id'])->account_id;
           
         if($no < count($transections))
                  $trans=$transections[$no];
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id=$depart;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$amount;
           $trans->save();
           $no++;
                     }

                      //gst transection

           $gst=$purchase->gst_tax_amount();
           if($request->vendor_id!='' && $gst > 0 )
               {
               $customer_acc=Vendor::find($request->vendor_id)->account_id;

              $remarks='GST against : '.$purchase['vendor']['name'].', '.$purchase->doc_no;
              

                  if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$gst;
           $trans->credit=0;
           $trans->save();
               

               if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id='680';
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$gst;
           $trans->save();
               

           }

                     

           $tax=$purchase->with_hold_tax_amount();
           if($request->vendor_id!='' && $tax > 0 )
               {
               $customer_acc=Vendor::find($request->vendor_id)->account_id;

              $remarks='Tax Deduction against : '.$purchase['vendor']['name'].', '.$purchase->doc_no;
              

                  if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id=$customer_acc;
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=0;
           $trans->credit=$tax;
           $trans->save();
               

               if($no < count($transections))
                  {$trans=$transections[$no]; $no++;}
                   else
                   $trans=new Transection;

           $trans->account_voucherable_id=$purchase->id;
           $trans->account_voucherable_type='App\Models\Purchasereturn';
           $trans->account_id='274';
           //$trans->corporate_id=$item['id'];
           $trans->remarks=$remarks;
           $trans->debit=$tax;
           $trans->credit=0;
           $trans->save();
               

           }

        
             for($i=$no; $i < count($transections); $i++ )
           {
               $transections[$i]->delete();
           }

            

                  return redirect()->back()->with('success','Purchase return updated!');
    }

    public function purchase_return_report(Purchasereturn $return)
    {
       
          $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();
             
             $purchase=$return;
        $data = [
            
            'purchase'=>$purchase,
             'name'=>$name,
            'address'=>$address,
              'logo'=>$logo,
        ];
        //return view('sale.estimated_invoice',compact('data'));
        
           view()->share('purchase.purchase_return_report',$data);
        $pdf = PDF::loadView('purchase.purchase_return_report', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('purchase.purchase_return_report.pdf');

      //return view('purchase.edit_purchase',compact('vendors','inventories','departments','purchase','grn','expenses'));
    }


    public function purchase_ledger(Request $request)
    {
         $from=$request->from;
         $to=$request->to;

         
           
           if($from!='' || $to!='')
         $lists=Purchase::where('doc_date','>=',$from)->where('doc_date','<=',$to)->orderBy('doc_date','asc')->get();
       else
        $lists=Purchase::orderBy('doc_date','asc')->get();

         $config=array('from'=>$from,'to'=>$to);

         return view('purchase.purchase_ledger',compact('lists','config'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
