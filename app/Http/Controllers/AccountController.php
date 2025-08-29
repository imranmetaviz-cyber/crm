<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Models\Transection;
use App\Models\InventoryDepartment;
use PDF;

class AccountController extends Controller
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

    public function chart_of_accounts()
    {
      $main_accounts=Account::where('type','main')->orderBy('code')->get();

      return view('accounts.chart_of_accounts',compact('main_accounts'));
    }

    public function chart_of_accounts_report()
    {
      $main_accounts=Account::where('type','main')->orderBy('code')->get();

      $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();


       $data = [
            
            'main_accounts'=>$main_accounts,
            'name'=>$name,
            'address'=>$address,
             'logo'=>$logo,
        
        ];
        
        
           view()->share('accounts.chart_acc_report',$data);
        $pdf = PDF::loadView('accounts.chart_acc_report', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('accounts.chart_acc_report.pdf');

      //return view('accounts.chart_acc_report',compact('main_accounts'));
    }

    public function main_accounts()
    {
        $accounts=Account::where('type','main')->get()->sortBy('account_type.name');
        $types=Configuration::where('type','like','main_account_type')->where('activeness',1)->get();
        return view('accounts.main',compact('accounts','types'));
    }

    public function main_account_save(Request $request)
    {
        $active=$request->activeness;
        if($active=='')
            $active=0;

        $acc=new Account;
          $acc->acc_type=$request->acc_type;
          $acc->code=$request->code;
          $acc->name=$request->name;
          $acc->type='main';
          $acc->remarks=$request->remarks;

          $acc->activeness=$active;
          $acc->save();

        return redirect()->back()->with('success','Main Account Added!');
    }

    public function edit_main_account(Account $account)
    {
        $accounts=Account::where('type','main')->get()->sortBy('account_type.name');
        $types=Configuration::where('type','like','main_account_type')->where('activeness',1)->get();
        return view('accounts.edit_main',compact('accounts','account','types'));
    }

    public function main_account_update(Request $request)
    {
        $active=$request->activeness;
        if($active=='')
            $active=0;

        $acc=Account::find($request->id);

          $acc->acc_type=$request->acc_type;
          $acc->code=$request->code;
          $acc->name=$request->name;
          $acc->type='main';
          $acc->remarks=$request->remarks;

          $acc->activeness=$active;
          $acc->save();

        return redirect()->back()->with('success','Main Account Updated!');
    }

    public function delete_main_account(Account $account)
    {
       

          if( count($account->sub_accounts) > 0  )
            return redirect()->back()->withErrors(['error' => 'Account have sub accounts!']);

          $account->delete();
          
        return redirect(url('/main/accounts'))->with('success','Main Account Deleted!');
    }

    public function sub_accounts()
    {
        $main_accounts=Account::where('type','main')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
        $sub_accounts=Account::where('type','sub')->get()->sortBy('main_account.name');


        return view('accounts.sub',compact('main_accounts','sub_accounts'));
    }

    public function edit_sub_account(Account $account)
    {
        $main_accounts=Account::where('type','main')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
        $sub_accounts=Account::where('type','sub')->where('super_id',$account['super_id'])->get()->sortBy('main_account.name');


        return view('accounts.edit_sub',compact('main_accounts','sub_accounts','account'));
    }

    public function sub_account_save(Request $request)
    {
        $active=$request->activeness;
        if($active=='')
            $active=0;

        $code=$request->main_code.'-'.$request->sub_code;

        $acc=new Account;
          $acc->super_id=$request->main_acc;
          $acc->code=$code;
          $acc->name=$request->name;
          $acc->type='sub';
          $acc->remarks=$request->remarks;

          $acc->activeness=$active;
          $acc->save();

        return redirect()->back()->with('success','Sub Account Added!');
    }

    public function sub_account_update(Request $request)
    {
        $active=$request->activeness;
        if($active=='')
            $active=0;

        $code=$request->main_code.'-'.$request->sub_code;

        $acc=Account::find($request->id);
          $acc->super_id=$request->main_acc;
          $acc->code=$code;
          $acc->name=$request->name;
          $acc->type='sub';
          $acc->remarks=$request->remarks;

          $acc->activeness=$active;
          $acc->save();

        return redirect()->back()->with('success','Sub Account Updated!');
    }

    public function delete_sub_account(Account $account)
    {
       

          if( count($account->sub_accounts) > 0  )
            return redirect()->back()->withErrors(['error' => 'Account have sub accounts!']);

          $account->delete();
          
        return redirect(url('/sub/accounts'))->with('success','Sub Account Deleted!');
    }

    public function sub_sub_accounts()
    {
        $sub_accounts=Account::where('type','sub')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
        $sub_sub_accounts=Account::where('type','sub_sub')->orderBy('name','asc')->orderBy('created_at','desc')->get()->sortBy('code')->sortBy('sub_account.name');

        $types=Configuration::where('type','like','sub_sub_account_type')->where('activeness',1)->get();

            
        return view('accounts.sub_sub',compact('sub_sub_accounts','sub_accounts','types'));
    }

    public function sub_sub_account_save(Request $request)
    {
        $active=$request->activeness;
        if($active=='')
            $active=0;

        $code=$request->sub_code.'-'.$request->sub_sub_code;

        $acc=new Account;
          $acc->super_id=$request->sub_acc;
          $acc->code=$code;
          $acc->name=$request->name;
          $acc->type='sub_sub';
          $acc->acc_type=$request->acc_type;
          $acc->remarks=$request->remarks;

          $acc->activeness=$active;

          $acc->save();
            // print_r($acc['id']);die;
        return redirect()->back()->with('success','Sub Sub Account Added!');
    }

    public function edit_sub_sub_account(Account $account)
    {
        $sub_accounts=Account::where('type','sub')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
        $sub_sub_accounts=Account::where('type','sub_sub')->where('super_id',$account['super_id'])->orderBy('name','asc')->orderBy('created_at','desc')->get()->sortBy('code')->sortBy('sub_account.name');

        $types=Configuration::where('type','like','sub_sub_account_type')->where('activeness',1)->get();

            
        return view('accounts.edit_sub_sub',compact('account','sub_sub_accounts','sub_accounts','types'));
    }

    public function sub_sub_account_update(Request $request)
    {
        $active=$request->activeness;
        if($active=='')
            $active=0;

        $code=$request->sub_code.'-'.$request->sub_sub_code;

        $acc=Account::find($request->id);
          $acc->super_id=$request->sub_acc;
          $acc->code=$code;
          $acc->name=$request->name;
          $acc->type='sub_sub';
          $acc->acc_type=$request->acc_type;
          $acc->remarks=$request->remarks;

          $acc->activeness=$active;

          $acc->save();
            // print_r($acc['id']);die;
        return redirect()->back()->with('success','Sub Sub Account updated!');
    }

     public function delete_sub_sub_account(Account $account)
    {
       
          //$let=[186,119,120];

          if($account['id']==119 || $account['id']==120 || $account['id']==186 )
          return redirect()->back()->withErrors(['error' => 'Account canot be deleted!']);

       

          if( count($account->sub_accounts) > 0  )
            return redirect()->back()->withErrors(['error' => 'Account have sub accounts!']);

          $account->delete();
          
        return redirect(url('/sub/sub/accounts'))->with('success','Sub Sub Account Deleted!');
    }

    public function get_account_code(Request $request)
    {
        $account=$request->account;

          $type=Account::find($account)->type;

        $sub_accounts=Account::with('super_account','account_type')->where('super_id',$account)->get();

        $code='';

        if(count($sub_accounts)==0)
        {
              if($type=='sub_sub')
                $code='00001';
                else
            $code='001';
        }
        else
        {
        $acc=$sub_accounts->sortByDesc('code')->first();

              $let=explode('-', $acc['code']);

              $let_count=count($let);

              $code=$let[$let_count-1] + 1 ;
               
               if($type=='sub_sub')
                $code=sprintf('%05d', $code);
                else
              $code=sprintf('%03d', $code);
          }

          $data=array('code'=>$code,'sub_accounts'=>$sub_accounts);

        return response()->json($data, 200);
    }

    public function get_account(Request $request)
    {
           $account_id=$request->account_id;

           

            $account=Account::find($account_id);


           return response()->json($account, 200);
    }

    public function get_detail_accounts(Request $request)
    {
           $sub_sub_acount_id=$request->sub_sub_acount_id;

           if($sub_sub_acount_id=='')
             $sub_sub_acount_id='%';

            $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->where('super_id','like',$sub_sub_acount_id)->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();


           return response()->json($accounts, 200);
    }

    public function detail_accounts()
    {
        $sub_sub_accounts=Account::where('type','sub_sub')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
        $detail_accounts=Account::where('type','detail')->orderBy('name','asc')->orderBy('created_at','desc')->get()->sortBy('code')->sortBy('sub_sub_account.name');

        

            
        return view('accounts.detail',compact('sub_sub_accounts','detail_accounts'));
    }

    public function detail_account_save(Request $request)
    {
        $active=$request->activeness;
        if($active=='')
            $active=0;

        $code=$request->sub_sub_code.'-'.$request->detail_code;

        $acc=new Account;
          $acc->super_id=$request->sub_sub_acc;
          $acc->code=$code;
          $acc->name=$request->name;
          $acc->type='detail';
          $acc->opening_balance=$request->opening_balance;
          $acc->remarks=$request->remarks;

          $acc->activeness=$active;

          $acc->save();
            // print_r($acc['id']);die;
        return redirect()->back()->with('success','Detail Account Added!');
    }

    public function edit_detail_account(Account $account)
    {
        $sub_sub_accounts=Account::where('type','sub_sub')->where('activeness',1)->orderBy('name','asc')->orderBy('created_at','desc')->get();
        $detail_accounts=Account::where('type','detail')->where('super_id',$account['super_id'])->orderBy('name','asc')->orderBy('created_at','desc')->get()->sortBy('code')->sortBy('sub_sub_account.name');

        

            
        return view('accounts.edit_detail',compact('account','sub_sub_accounts','detail_accounts'));
    }

    public function detail_account_update(Request $request)
    {
        $active=$request->activeness;
        if($active=='')
            $active=0;

        $code=$request->sub_sub_code.'-'.$request->detail_code;

        $acc=Account::find($request->id);
          $acc->super_id=$request->sub_sub_acc;
          $acc->code=$code;
          $acc->name=$request->name;
          $acc->type='detail';
          $acc->opening_balance=$request->opening_balance;
          $acc->remarks=$request->remarks;

          $acc->activeness=$active;

          $acc->save();
            // print_r($acc['id']);die;
        return redirect()->back()->with('success','Detail Account Updated!');
    }

    public function delete_detail_account(Account $account)
    {
       
          if($account->customer!='' || $account->vendor!='' )
            return redirect()->back()->withErrors(['error' => 'Account attached with vendor/customer!']);

          if( count($account->expenses) > 0  )
            return redirect()->back()->withErrors(['error' => 'Account attached with expense!']);


          $transections=Transection::where('account_id',$account['id'])->first();

          if($transections!='' )
          return redirect()->back()->withErrors(['error' => 'Account have transections, so it canot be deleted!']);

        $acc=InventoryDepartment::where('account_id',$account['id'])->first();
         $cgs=InventoryDepartment::where('cgs_account_id',$account['id'])->first();
          $sale=InventoryDepartment::where('sale_account_id',$account['id'])->first();

          if($acc != '' || $cgs != '' || $sale != '')
            return redirect()->back()->withErrors(['error' => 'Account attached with item department!']);

          $account->delete();
          
        return redirect(url('detail/accounts'))->with('success','Detail Account Deleted!');
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
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    public function trail_balance(Request $request)
    {
        $from=$request->from;
        $to=$request->to;
        $exclude_zero=$request->exclude_zero;
        $level=$request->level;
          
          if($from=='' && $to=='')
         {
            return view('accounts.trail_balance');
        }
           $accounts=array();
      $detail_accounts=Account::where(function($q) use($level){
            if($level=='detail')
           $q->where('type','detail');
         if($level=='sub_sub')
           $q->where('type','sub_sub');
         if($level=='sub')
           $q->where('type','sub');
         if($level=='main')
           $q->where('type','main');

      })->orderBy('name','asc')->orderBy('created_at','desc')->get();

      // $detail_accounts=Account::where('code','28-007-006')->orderBy('name','asc')->orderBy('created_at','desc')->get();

      //$detail_accounts=Account::where('type','main')->orderBy('name','asc')->orderBy('created_at','desc')->get();

        //print_r(json_encode( $detail_accounts) );die;
          foreach ($detail_accounts as $acc) {
            
            $from_=date('Y-m-d', strtotime($from. ' -1 days'));
            //$opening_trail=$acc->trail_balance('',$from_);//opening of from

            $current_trail=$acc->trail_balance($from,$to);//curent from to to

            //$balance_trail=$acc->trail_balance('',$to);//opening of from

            $opening_balance=$acc->opening_balance($from);//opening of from
            $closing_balance=$acc->closing_balance($to);//opening of from



            //$account=array('id'=>$acc['id'],'sub_acc'=>$acc['super_account']['name'],'code'=>$acc['code'],'name'=>$acc['name'],'opening_trail_debit'=>$opening_trail['debit'],'opening_trail_credit'=>$opening_trail['credit'],'current_trail_debit'=>$current_trail['debit'],'current_trail_credit'=>$current_trail['credit'],'balance_trail_debit'=>$balance_trail['debit'],'balance_trail_credit'=>$balance_trail['credit']);
            $account=array('id'=>$acc['id'],'code'=>$acc['code'],'name'=>$acc['name'],'opening_balance'=>$opening_balance,'current_trail_debit'=>$current_trail['debit'],'current_trail_credit'=>$current_trail['credit'],'closing_balance'=>$closing_balance);
            
           if($exclude_zero==1)
           {
              if($opening_balance==0 && $closing_balance==0 && $current_trail['debit']==0 && $current_trail['credit']==0)
            { continue; }
           }
           
           
             array_push($accounts, $account);
           
            
              
          }

          $config=array('from'=>$from,'to'=>$to,'exclude_zero'=>$exclude_zero,'level'=>$level);

          return view('accounts.trail_balance',compact('accounts','config'));

    }

    public function trail_balance_report(Request $request)
    {
        $from=$request->from;
        $to=$request->to;
        $exclude_zero=$request->exclude_zero;
         $level=$request->level;

        $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();

          
          if($from=='' && $to=='')
         {
             //return view('accounts.trail_balance');
          }

           $accounts=array();
          $detail_accounts=Account::where(function($q) use($level){
            if($level=='detail')
           $q->where('type','detail');
         if($level=='sub_sub')
           $q->where('type','sub_sub');
         if($level=='sub')
           $q->where('type','sub');
         if($level=='main')
           $q->where('type','main');

      })->orderBy('name','asc')->orderBy('created_at','desc')->get();

          foreach ($detail_accounts as $acc) {
            
            $from_=date('Y-m-d', strtotime($from. ' -1 days'));
            //$opening_trail=$acc->trail_balance('',$from_);//opening of from

            $current_trail=$acc->trail_balance($from,$to);//curent from to to

            //$balance_trail=$acc->trail_balance('',$to);//opening of from

            $opening_balance=$acc->opening_balance($from);//opening of from
            $closing_balance=$acc->closing_balance($to);//opening of from

            //$account=array('id'=>$acc['id'],'sub_acc'=>$acc['super_account']['name'],'code'=>$acc['code'],'name'=>$acc['name'],'opening_trail_debit'=>$opening_trail['debit'],'opening_trail_credit'=>$opening_trail['credit'],'current_trail_debit'=>$current_trail['debit'],'current_trail_credit'=>$current_trail['credit'],'balance_trail_debit'=>$balance_trail['debit'],'balance_trail_credit'=>$balance_trail['credit']);
             $account=array('id'=>$acc['id'],'code'=>$acc['code'],'name'=>$acc['name'],'opening_balance'=>$opening_balance,'current_trail_debit'=>$current_trail['debit'],'current_trail_credit'=>$current_trail['credit'],'closing_balance'=>$closing_balance);

              if($exclude_zero==1)
           {
              if($opening_balance==0 && $closing_balance==0 && $current_trail['debit']==0 && $current_trail['credit']==0)
            { continue; }
           }

            array_push($accounts, $account);
          }

          $config=array('from'=>$from,'to'=>$to, 'exclude_zero'=>$exclude_zero);

          $data = [
            
            'config'=>$config,
              'accounts'=>$accounts,
              'name'=>$name,
            'address'=>$address,
             'logo'=>$logo,
        ];
        
        
           view()->share('accounts.trail_balance_report',$data);
        $pdf = PDF::loadView('accounts.trail_balance_report', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('accounts.trail_balance_report.pdf');

          //return view('accounts.trail_balance',compact('accounts','config'));

    }


    public function account_ledger(Request $request)
    {
        $from=$request->from;
        $to=$request->to;
        $account_id=$request->account_id;
         $detail=$request->detail;

         if($detail=='')
           $detail=0;
      

        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->orderBy('name','asc')->orderBy('created_at','desc')->get();
          
          if($from=='' && $to=='' && $account_id=='')
         {
            return view('accounts.account_ledger',compact('accounts'));
         }//end if
          elseif($account_id!='')
        {
            
            $account=Account::find($account_id);

            $ledger=$account->ledger($from,$to,$detail);
          
          $config=array('from'=>$from,'to'=>$to,'account'=>$account,'detail'=>$detail);

          return view('accounts.account_ledger',compact('accounts','ledger','config'));
        }//end else if

    }


    public function account_ledger_report(Request $request)
    {
        $from=$request->from;
        $to=$request->to;
        $account_id=$request->account_id;
         $detail=$request->detail;

         $name=Configuration::company_full_name();
        $address=Configuration::company_factory_address();
        $logo=Configuration::company_logo();


         if($detail=='')
           $detail=0;
      

        $accounts=Account::with('super_account','super_account.account_type')->where('type','detail')->orderBy('name','asc')->orderBy('created_at','desc')->get();
          
         
            $account=[]; $ledger=[];
      
          if($account_id!='')
        {
            
            $account=Account::find($account_id);

            $ledger=$account->ledger($from,$to,$detail);
          
        
        }//end else if

        $config=array('from'=>$from,'to'=>$to,'account'=>$account,'detail'=>$detail);

          $data = [
            
            'config'=>$config,
              'ledger'=>$ledger,
              'name'=>$name,
            'address'=>$address,
            'logo'=>$logo,
        ];


        view()->share('accounts.ledger_report',$data);
        $pdf = PDF::loadView('accounts.ledger_report', $data);
        $pdf->setPaper('A4','portrait');
        return $pdf->stream('accounts.ledger_report.pdf');

    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }
}

