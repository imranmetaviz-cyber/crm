<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Models\Account;
use App\Models\Transection;
use PDF;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vouchers=Voucher::where('category','voucher')->orderBy('created_at','desc')->get();
        
        return view('voucher.history',compact('vouchers'));
    }

    public function voucher_types()
    {
        $types=Configuration::where('type','like','voucher_type')->get();

        return view('voucher.voucher_type',compact('types'));
    }

    public function voucher_type_save(Request $request)
    {
        $activeness=$request->get('activeness');
        if($activeness=='')
            $activeness='0';
        $depart=new Configuration;

        $depart->name=$request->get('name');
        $depart->type='voucher_type';
        $depart->attributes=$request->get('code');
        $depart->description=$request->get('remarks');
    

        $depart->activeness=$activeness;

       

        $depart->save();

     return redirect()->back()->with('success','Voucher Type Added!');
    }

    public function edit_voucher_type($id)
    {
        $types=Configuration::where('type','like','voucher_type')->get();

        $type=Configuration::find($id);

        return view('voucher.edit_voucher_type',compact('types','type'));
    }

    public function voucher_type_update(Request $request)
    {
        $activeness=$request->get('activeness');

        if($activeness=='')
            $activeness='0';

        $depart=Configuration::find($request->get('id'));

        $depart->name=$request->get('name');
        $depart->type='voucher_type';
        $depart->attributes=$request->get('code');
        $depart->description=$request->get('remarks');
    

        $depart->activeness=$activeness;

       

        $depart->save();

     return redirect()->back()->with('success','Voucher Type Updated!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sub_sub_accounts=Account::where('type','sub_sub')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        $types=Configuration::where('type','like','voucher_type')->where('activeness',1)->get();

         return view ('voucher.voucher',compact('sub_sub_accounts','accounts','types'));
    }

    public function get_voucher_no(Request $request)
    {
          $voucher_type_id=$request->voucher_type;
         $voucher_type=Configuration::find($voucher_type_id);
         $voucher_type_code=$voucher_type['attributes'];
          
           $voucher_date=$request->voucher_date;
            $let=explode('-', $voucher_date);
            $month=$let[1];
            $year=$let[0];

          $doc_no=$voucher_type_code."-".Date("y",strtotime($voucher_date))."-".$month."-";
           $num=1;

           $voucher=Voucher::where('voucher_type_id',$voucher_type['id'])->where('voucher_no','like',$doc_no.'%')->orderBy('voucher_no','desc')->first();

         
         if($voucher=='')
         {
              $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $voucher['voucher_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
            //voucher type will use in Voucher
         $data=array('doc_no'=>$doc_no,'voucher_type'=>$voucher_type);

        return response()->json($data, 200);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $chln=Voucher::where('voucher_no',$request->voucher_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Voucher no. already existed!');
         
        $sub_accounts_id=$request->sub_accounts_id;

       $accounts_id=$request->accounts_id;
       $remarks=$request->remarks;
        $cheque_no=$request->cheque_no;
        $cheque_date=$request->cheque_date;

        $debit=$request->debit;
        $credit=$request->credit;

         
         $active=$request->activeness;
        if($active=='')
            $active='0';

        $voucher=new Voucher;

         $voucher->voucher_type_id=$request->voucher_type;
        $voucher->voucher_date=$request->voucher_date;
        $voucher->voucher_no=$request->voucher_no;
        $voucher->activeness=$active;
        $voucher->category='voucher';
        
        $voucher->save();
            
            for($i=0;$i<count($accounts_id);$i++)
            {
         $voucher->accounts()->attach($accounts_id[$i] , ['account_voucherable_id'=>$voucher['id'],'account_voucherable_type'=>'App\Models\Voucher','remarks' => $remarks[$i] , 'cheque_no' => $cheque_no[$i] ,'cheque_date'=>$cheque_date[$i] ,'debit'=>$debit[$i] ,'credit'=>$credit[$i]  ]);
           }
        
        return redirect()->back()->with(['success'=>'Voucher genrated!','voucher_id'=>$voucher['id'] ]);
         return redirect('/edit/voucher/'.$voucher['id'])->with('success','Voucher genrated!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function edit(Voucher $voucher)
    {
        $sub_sub_accounts=Account::where('type','sub_sub')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        $types=Configuration::where('type','like','voucher_type')->where('activeness',1)->get();

         return view ('voucher.edit_voucher',compact('voucher','sub_sub_accounts','accounts','types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $sub_accounts_id=$request->sub_accounts_id;

       $accounts_id=$request->accounts_id;
       $remarks=$request->remarks;
        $cheque_no=$request->cheque_no;
        $cheque_date=$request->cheque_date;

        $debit=$request->debit;
        $credit=$request->credit;
        $pivot_ids=$request->pivots_id;
         
         $active=$request->activeness;
        if($active=='')
            $active='0';

        $voucher=Voucher::find($request->id);

         $voucher->voucher_type_id=$request->voucher_type;
        $voucher->voucher_date=$request->voucher_date;
        $voucher->voucher_no=$request->voucher_no;
        $voucher->activeness=$active;
        $voucher->category='voucher';

        $voucher->save();
    

           $items=Transection::where('account_voucherable_id',$voucher['id'])->where('account_voucherable_type','App\Models\Voucher')->whereNotIn('id',$pivot_ids)->get();

        foreach ($items as $tr) {
                $tr->delete();
        }

           for ($i=0;$i<count($accounts_id);$i++)
           {
                 if($pivot_ids[$i]!=0)
                 $item=Transection::find($pivot_ids[$i]);
                  else
                  $item=new Transection;

                $item->voucher_id=$voucher['id'];
                $item->account_id=$accounts_id[$i];

                $item->account_voucherable_id=$voucher['id'];
                $item->account_voucherable_type='App\Models\Voucher';
                $item->remarks=$remarks[$i];
                $item->cheque_no=$cheque_no[$i];
                $item->cheque_date=$cheque_date[$i];
                $item->debit=$debit[$i];
            
                $item->credit=$credit[$i];
                $item->save();
           }

          return redirect()->back()->with('success','Voucher updated!');
         
    }

    public function print_voucher(Voucher $voucher)
    {

        $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();
        
        $data = [
            
            'voucher'=>$voucher,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        ];
        
           view()->share('voucher.voucher_report',$data);
        $pdf = PDF::loadView('voucher.voucher_report', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('voucher.voucher_report.pdf');

         
    }

    public function print_voucher1(Voucher $voucher)
    {

        $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();
        
        $data = [
            
            'voucher'=>$voucher,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        ];
        
           view()->share('voucher.voucher_report1',$data);
        $pdf = PDF::loadView('voucher.voucher_report1', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('voucher.voucher_report1.pdf');

         
    }

    public function print_voucher2(Voucher $voucher)
    {

        $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();
        
        $data = [
            
            'voucher'=>$voucher,
            'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        ];
        
           view()->share('voucher.voucher_report2',$data);
        $pdf = PDF::loadView('voucher.voucher_report2', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('voucher.voucher_report2.pdf');

         
    }

    public function create_payment()
    {

         $voucher_type=Configuration::find(27);
         $voucher_type_code=$voucher_type['attributes'];

        

          $doc_no=$voucher_type_code."-".Date("y")."-".Date("m")."-";
           $num=1;
           $voucher=Voucher::where('voucher_type_id',$voucher_type['id'])->where('voucher_no','like',$doc_no.'%')->orderBy('voucher_no','desc')->first();

         
         if($voucher=='')
         {
              $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $voucher['voucher_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }


        $cashes=Account::where('super_id',119)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();


        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        

         return view ('voucher.payment_create',compact('cashes','accounts','doc_no'));
    }

    public function store_payment(Request $request)
    {
        
       $chln=Voucher::where('voucher_no',$request->voucher_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Voucher no. already existed!');

       $accounts_id=$request->accounts_id;
       $remarks=$request->remarks;
        $cheque_no=$request->cheque_no;
        $cheque_date=$request->cheque_date;

        $amount=$request->amount;
                 
         $active=$request->activeness;
        if($active=='')
            $active='0';

        $pay_method=$request->pay_method;
        $pay_from=$request->pay_from;
          
          if($pay_method=='cash')
          { $voucher_type_id=27; }
          elseif($pay_method=='bank')
            { $voucher_type_id=29; }
       //print_r(json_encode($pay_from));die;
        $voucher=new Voucher;

         $voucher->voucher_type_id=$voucher_type_id;
         $voucher->pay_method=$pay_method;
        $voucher->voucher_date=$request->voucher_date;
        $voucher->voucher_no=$request->voucher_no;
        $voucher->remarks=$request->reference;
        $voucher->activeness=$active;
        $voucher->category='payment';
        
        $voucher->save();
            
            $net_amount=0; //$cheque_no1=''; $cheque_date1='';
            for($i=0; $i<count($accounts_id); $i++)
            {
         $voucher->accounts()->attach($accounts_id[$i] , ['account_voucherable_id'=>$voucher['id'],'account_voucherable_type'=>'App\Models\Voucher','remarks' => $remarks[$i] , 'cheque_no' => $cheque_no[$i] ,'cheque_date'=>$cheque_date[$i] ,'debit'=>$amount[$i] ,'credit'=>0  ]);

          $voucher->accounts()->attach($pay_from , ['account_voucherable_id'=>$voucher['id'],'account_voucherable_type'=>'App\Models\Voucher','remarks' => $remarks[$i] , 'cheque_no' => $cheque_no[$i] ,'cheque_date'=>$cheque_date[$i] ,'debit'=>0 ,'credit'=>$amount[$i]  ]);

         
             // $net_amount +=$amount[$i] ; 
             // $cheque_no1 =$cheque_no1 .','. $cheque_no[$i] ;
             //  $cheque_date1 =$cheque_date1 .','. $cheque_date[$i] ;
             
           }

           //$cheque_no1=substr($cheque_no1,1);
                 //$cheque_date1=substr($cheque_date1,1);

          

         return redirect()->back()->with(['success'=>'Payment genrated!','payment_id'=>$voucher['id'] ]);
         return redirect('/edit/payment/'.$voucher['id'])->with('success','Payment genrated!');
    }

    public function index_payment()
    {
        $vouchers=Voucher::where('category','payment')->orderBy('voucher_date','desc')->get();
        
        return view('voucher.payment_history',compact('vouchers'));
    }

    public function edit_payment(Voucher $voucher)
    {
        $cashes=[];
        if($voucher['pay_method']=='cash')
        $cashes=Account::where('super_id',119)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
         if($voucher['pay_method']=='bank')
        $cashes=Account::where('super_id',120)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        $pay_from=$voucher['accounts']->where('pivot.credit','<>',0)->first();
        //$transections=$voucher['accounts']->where('pivot.debit','<>',0);

        $debit_transections=$voucher['accounts']->where('pivot.debit','<>',0);
        $credit_transection=$voucher['accounts']->where('pivot.credit','<>',0)->first();
        
        $payment=array('id'=>$voucher['id'] , 'voucher_no'=>$voucher['voucher_no'] , 'voucher_date'=>$voucher['voucher_date'] , 'pay_method'=>$voucher['pay_method'] ,'activeness'=>$voucher['activeness'] ,'reference'=>$voucher['remarks'] , 'pay_from'=>$pay_from['id'] , 'debit_transections'=>$debit_transections, 'credit_transection'=>$credit_transection  );

        

         return view ('voucher.edit_payment',compact('payment','cashes','accounts'));
    }

    public function update_payment(Request $request)
    {
        

       $accounts_id=$request->accounts_id;
       $remarks=$request->remarks;
        $cheque_no=$request->cheque_no;
        $cheque_date=$request->cheque_date;

        $amount=$request->amount;
        $pivot_ids=$request->pivots_id;
        $credit_pivot_id=$request->credit_pivot_id;
//print_r(json_encode($cheque_no));die;
         
         $active=$request->activeness;
        if($active=='')
            $active='0';

        $pay_method=$request->pay_method;
        $pay_from=$request->pay_from;
          
          if($pay_method=='cash')
          { $voucher_type_id=27; }
          elseif($pay_method=='bank')
            { $voucher_type_id=29; }

        $voucher=Voucher::find($request->id);

         $voucher->voucher_type_id=$voucher_type_id;
         $voucher->pay_method=$pay_method;
        $voucher->voucher_date=$request->voucher_date;
        $voucher->voucher_no=$request->voucher_no;
        $voucher->remarks=$request->reference;
        $voucher->activeness=$active;
        $voucher->category='payment';
        
        $voucher->save();
            
        

          $transections=$voucher->transections;
            $no=0;

               $net_amount=0; $cheque_no1=''; $cheque_date1='';
           for ($i=0;$i<count($accounts_id);$i++)
           {
                 if($no < count($transections))
                 $item=$transections[$no];
                  else
                  $item=new Transection;

                $item->voucher_id=$voucher['id'];
                $item->account_id=$accounts_id[$i];
                $item->account_voucherable_id=$voucher['id'];
                $item->account_voucherable_type='App\Models\Voucher';
                $item->remarks=$remarks[$i];
                $item->cheque_no=$cheque_no[$i];
                $item->cheque_date=$cheque_date[$i];
                $item->debit=$amount[$i];
                $item->credit=0;
                $item->save();
                $no++;
            

                if($no < count($transections))
                $item1=$transections[$no];
                  else
                  $item1=new Transection;

                $item1->voucher_id=$voucher['id'];
                $item1->account_id=$pay_from;
                $item1->account_voucherable_id=$voucher['id'];
                $item1->account_voucherable_type='App\Models\Voucher';
                $item1->remarks=$remarks[$i];
                $item1->cheque_no=$cheque_no[$i];
                $item1->cheque_date=$cheque_date[$i]; 
                $item1->debit=0;
                $item1->credit=$amount[$i];
                $item1->save();

                $no++;
                
           }

      

           for($i=$no; $i < count($transections); $i++ )
           {
               $transections[$i]->delete();
           }
            


         return redirect()->back()->with('success','Payment updated!');
         
    }


    public function create_receipt()
    {

         $voucher_type=Configuration::find(28);
         $voucher_type_code=$voucher_type['attributes'];

       

          $doc_no=$voucher_type_code."-".Date("y")."-".Date("m")."-";
           $num=1;
            $voucher=Voucher::where('voucher_type_id',$voucher_type['id'])->where('voucher_no','like',$doc_no.'%')->orderBy('voucher_no','desc')->first();
//print_r(json_encode($voucher));die;
         
         if($voucher=='')
         {
              $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $voucher['voucher_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }


        $cashes=Account::where('super_id',119)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();


        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        

         return view ('voucher.receipt_create',compact('cashes','accounts','doc_no'));
    }

    public function store_receipt(Request $request)
    {
        
       $chln=Voucher::where('voucher_no',$request->voucher_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Voucher no. already existed!');

       $accounts_id=$request->accounts_id;
       $remarks=$request->remarks;
        $cheque_no=$request->cheque_no;
        $cheque_date=$request->cheque_date;

        $amount=$request->amount;
        
//print_r(json_encode($cheque_no));die;
         
         $active=$request->activeness;
        if($active=='')
            $active='0';

        $pay_method=$request->pay_method;
        $pay_to=$request->pay_to;
          
          if($pay_method=='cash')
          { $voucher_type_id=28; }
          elseif($pay_method=='bank')
            { $voucher_type_id=30; }

        $voucher=new Voucher;

         $voucher->voucher_type_id=$voucher_type_id;
         $voucher->pay_method=$pay_method;
        $voucher->voucher_date=$request->voucher_date;
        $voucher->voucher_no=$request->voucher_no;
        $voucher->remarks=$request->reference;
        $voucher->activeness=$active;
        $voucher->category='receipt';
        
        $voucher->save();
            
            $net_amount=0; 
            for($i=0; $i<count($accounts_id); $i++)
            {
         $voucher->accounts()->attach($accounts_id[$i] , ['account_voucherable_id'=>$voucher['id'],'account_voucherable_type'=>'App\Models\Voucher','remarks' => $remarks[$i] , 'cheque_no' => $cheque_no[$i] ,'cheque_date'=>$cheque_date[$i] ,'debit'=>0 ,'credit'=>$amount[$i]  ]);

          $voucher->accounts()->attach($pay_to , ['account_voucherable_id'=>$voucher['id'],'account_voucherable_type'=>'App\Models\Voucher','remarks' => $remarks[$i] , 'cheque_no' => $cheque_no[$i] ,'cheque_date'=>$cheque_date[$i] ,'debit'=>$amount[$i] ,'credit'=>0  ]);
            
           }

          
         return redirect()->back()->with(['success'=>'Receipt genrated!','receipt_id'=>$voucher['id'] ]);
         return redirect('/edit/receipt/'.$voucher['id'])->with('success','Receipt genrated!');
    }

    public function index_receipt()
    {
        $vouchers=Voucher::where('category','receipt')->orderBy('voucher_date','desc')->get();
        
        return view('voucher.receipt_history',compact('vouchers'));
    }

    public function edit_receipt(Voucher $voucher)
    {
        $cashes=[];
        if($voucher['pay_method']=='cash')
        $cashes=Account::where('super_id',119)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
         if($voucher['pay_method']=='bank')
        $cashes=Account::where('super_id',120)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        $pay_to=$voucher['accounts']->where('pivot.debit','<>',0)->first();
        $transections=$voucher['accounts']->where('pivot.credit','<>',0);

        $debit_transection=$voucher['accounts']->where('pivot.debit','<>',0)->first();
        $credit_transections=$voucher['accounts']->where('pivot.credit','<>',0);
        //print_r( json_encode( $transections ) );die;
        $payment=array('id'=>$voucher['id'] , 'voucher_no'=>$voucher['voucher_no'] , 'voucher_date'=>$voucher['voucher_date'] , 'pay_method'=>$voucher['pay_method'] ,'activeness'=>$voucher['activeness'] ,'reference'=>$voucher['remarks'] , 'pay_to'=>$pay_to['id'] , 'credit_transections'=>$credit_transections, 'debit_transection'=>$debit_transection  );

        

         return view ('voucher.edit_receipt',compact('payment','cashes','accounts'));
    }


    public function update_receipt(Request $request)
    {
        

       $accounts_id=$request->accounts_id;
       $remarks=$request->remarks;
        $cheque_no=$request->cheque_no;
        $cheque_date=$request->cheque_date;

        $amount=$request->amount;
        $pivot_ids=$request->pivots_id;
        $debit_pivot_id=$request->debit_pivot_id;
//print_r(json_encode($cheque_no));die;
         
         $active=$request->activeness;
        if($active=='')
            $active='0';

        $pay_method=$request->pay_method;
        $pay_to=$request->pay_to;
          
          if($pay_method=='cash')
          { $voucher_type_id=28; }
          elseif($pay_method=='bank')
            { $voucher_type_id=30; }


        $voucher=Voucher::find($request->id);

         $voucher->voucher_type_id=$voucher_type_id;
         $voucher->pay_method=$pay_method;
        $voucher->voucher_date=$request->voucher_date;
        $voucher->voucher_no=$request->voucher_no;
        $voucher->remarks=$request->reference;
        $voucher->activeness=$active;
        $voucher->category='receipt';
        
        $voucher->save();

         $transections=$voucher->transections;
            $no=0;
        
         $net_amount=0; $cheque_no1=''; $cheque_date1='';
          
           for ($i=0;$i<count($accounts_id);$i++)
           {
                   if($no < count($transections))
                $item=$transections[$no];
                  else
                  $item=new Transection;

                $item->voucher_id=$voucher['id'];
                $item->account_id=$accounts_id[$i];
                $item->account_voucherable_id=$voucher['id'];
                $item->account_voucherable_type='App\Models\Voucher';
                $item->remarks=$remarks[$i];
                $item->cheque_no=$cheque_no[$i];
                $item->cheque_date=$cheque_date[$i];
                $item->debit=0;
                $item->credit=$amount[$i];
                $item->save();
                  $no++;


                     if($no < count($transections))
                $item1=$transections[$no];
                  else
                  $item1=new Transection;
                $item1->voucher_id=$voucher['id'];
                $item1->account_id=$pay_to;
                $item1->account_voucherable_id=$voucher['id'];
                $item1->account_voucherable_type='App\Models\Voucher';
                $item1->remarks=$remarks[$i];
                $item1->cheque_no=$cheque_no[$i];
                $item1->cheque_date=$cheque_date[$i];
                $item1->debit=$amount[$i];
                $item1->credit=0;
                $item1->save();
                $no++;
              
           }

            
          
            
           for($i=$no; $i < count($transections); $i++ )
           {
               $transections[$i]->delete();
           }
    

                       
           

         return redirect()->back()->with('success','Receipt updated!');
         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        
        $category=$voucher->category;

        foreach ($voucher->transections as $trans) {
            $trans->delete();
        }
        $voucher->delete();

        if($category=='expense')
        return redirect(url('expense/create'))->with('success','Expense Deleted!');
        elseif($category=='payment')
        return redirect(url('payment/create'))->with('success','Payment Deleted!');
        elseif($category=='receipt')
        return redirect(url('receipt/create'))->with('success','Receipt Deleted!');
        elseif($category=='voucher')
        return redirect(url('voucher/create'))->with('success','Voucher Deleted!');
    }

    //expense start functions

    public function create_expense()
    {

         $voucher_type=Configuration::find(27);
         $voucher_type_code=$voucher_type['attributes'];

       

          $doc_no=$voucher_type_code."-".Date("y")."-".Date("m")."-";
           $num=1;

            $voucher=Voucher::where('voucher_type_id',$voucher_type['id'])->where('voucher_no','like',$doc_no.'%')->orderBy('voucher_no','desc')->first();

         
         if($voucher=='')
         {
              $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }
         else
         {
            $let=explode($doc_no , $voucher['voucher_no']);
            $num=intval($let[1]) + 1;
            $let=sprintf('%03d', $num);
              $doc_no=$doc_no. $let;
         }


        $cashes=Account::where('super_id',119)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();


        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        

         return view ('voucher.expense_create',compact('cashes','accounts','doc_no'));
    }

    public function store_expense(Request $request)
    {
        
       $chln=Voucher::where('voucher_no',$request->voucher_no)->first();
            if($chln!='')
             return redirect()->back()->with('error','Voucher no. already existed!');

       $accounts_id=$request->accounts_id;
       $remarks=$request->remarks;
        $cheque_no=$request->cheque_no;
        $cheque_date=$request->cheque_date;

        $amount=$request->amount;
                 
         $active=$request->activeness;
        if($active=='')
            $active='0';

        $pay_method=$request->pay_method;
        $pay_from=$request->pay_from;
          
          if($pay_method=='cash')
          { $voucher_type_id=27; }
          elseif($pay_method=='bank')
            { $voucher_type_id=29; }
       //print_r(json_encode($pay_from));die;
        $voucher=new Voucher;

         $voucher->voucher_type_id=$voucher_type_id;
         $voucher->pay_method=$pay_method;
        $voucher->voucher_date=$request->voucher_date;
        $voucher->voucher_no=$request->voucher_no;
        $voucher->remarks=$request->reference;
        $voucher->activeness=$active;
        $voucher->category='expense';
        
        $voucher->save();
            
            $net_amount=0;
            for($i=0; $i<count($accounts_id); $i++)
            {
         $voucher->accounts()->attach($accounts_id[$i] , ['account_voucherable_id'=>$voucher['id'],'account_voucherable_type'=>'App\Models\Voucher','remarks' => $remarks[$i] , 'cheque_no' => $cheque_no[$i] ,'cheque_date'=>$cheque_date[$i] ,'debit'=>$amount[$i] ,'credit'=>0  ]);

        $voucher->accounts()->attach($pay_from , ['account_voucherable_id'=>$voucher['id'],'account_voucherable_type'=>'App\Models\Voucher','remarks' => $remarks[$i] , 'cheque_no' => $cheque_no[$i] ,'cheque_date'=>$cheque_date[$i] ,'debit'=>0 ,'credit'=>$amount[$i]   ]);
             
           }

      
          
         return redirect()->back()->with(['success'=>'Expense genrated!','expense_id'=>$voucher['id'] ]);
         return redirect('/edit/expense/'.$voucher['id'])->with('success','Expense added!');
    }

    public function index_expense()
    {
        $vouchers=Voucher::where('category','expense')->orderBy('voucher_date','desc')->get();
        
        return view('voucher.expense_history',compact('vouchers'));
    }

    public function edit_expense(Voucher $voucher)
    {
        $cashes=[];
        if($voucher['pay_method']=='cash')
        $cashes=Account::where('super_id',119)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
         if($voucher['pay_method']=='bank')
        $cashes=Account::where('super_id',120)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();

        $pay_from=$voucher['accounts']->where('pivot.credit','<>',0)->first();

        $debit_transections=$voucher['accounts']->where('pivot.debit','<>',0);
        $credit_transection=$voucher['accounts']->where('pivot.credit','<>',0)->first();
        
        $payment=array('id'=>$voucher['id'] , 'voucher_no'=>$voucher['voucher_no'] , 'voucher_date'=>$voucher['voucher_date'] , 'pay_method'=>$voucher['pay_method'] ,'activeness'=>$voucher['activeness'] ,'reference'=>$voucher['remarks'] , 'pay_from'=>$pay_from['id'] , 'debit_transections'=>$debit_transections ,'credit_transection'=>$credit_transection );

        

         return view ('voucher.edit_expense',compact('payment','cashes','accounts'));
    }

    public function update_expense(Request $request)
    {
        

       $accounts_id=$request->accounts_id;
       $remarks=$request->remarks;
        $cheque_no=$request->cheque_no;
        $cheque_date=$request->cheque_date;
        $amount=$request->amount;
        $pivot_ids=$request->pivots_id;
         $credit_pivot_id=$request->credit_pivot_id;
//print_r(json_encode($cheque_no));die;
         
         $active=$request->activeness;
        if($active=='')
            $active='0';

        $pay_method=$request->pay_method;
        $pay_from=$request->pay_from;
          
          if($pay_method=='cash')
          { $voucher_type_id=27; }
          elseif($pay_method=='bank')
            { $voucher_type_id=29; }

        $voucher=Voucher::find($request->id);

         $voucher->voucher_type_id=$voucher_type_id;
         $voucher->pay_method=$pay_method;
        $voucher->voucher_date=$request->voucher_date;
        $voucher->voucher_no=$request->voucher_no;
        $voucher->remarks=$request->reference;
        $voucher->activeness=$active;
        $voucher->category='expense';
        
        $voucher->save();
            
           



           $transections=$voucher->transections;
            $no=0;

             

           for ($i=0;$i<count($accounts_id);$i++)
           {
                 if($no < count($transections))
                 $item=$transections[$no];
                  else
                  $item=new Transection;

                $item->voucher_id=$voucher['id'];
                $item->account_id=$accounts_id[$i];
                $item->account_voucherable_id=$voucher['id'];
                $item->account_voucherable_type='App\Models\Voucher';
                $item->remarks=$remarks[$i];
                $item->cheque_no=$cheque_no[$i];
                $item->cheque_date=$cheque_date[$i];
                $item->debit=$amount[$i];
                $item->credit=0;
                $item->save();
                 $no++;

                if($no < count($transections))
                 $item1=$transections[$no];
                  else
                  $item1=new Transection;

                $item1->voucher_id=$voucher['id'];
                $item1->account_id=$pay_from;
                $item1->account_voucherable_id=$voucher['id'];
                $item1->account_voucherable_type='App\Models\Voucher';
                $item1->remarks=$remarks[$i];
                $item1->cheque_no=$cheque_no[$i];
                $item1->cheque_date=$cheque_date[$i];
                $item1->debit=0;
                $item1->credit=$amount[$i];
                $item1->save(); 

                $no++; 
                
           }

            for($i=$no; $i < count($transections); $i++ )
           {
               $transections[$i]->delete();
           }


                      

         return redirect()->back()->with('success','Expense updated!');
         
    }

    //end expense funtions
}
