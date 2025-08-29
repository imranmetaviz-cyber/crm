<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

  public function super_account()
    {
             return $this->belongsTo('App\Models\Account','super_id');
    }
    
    public function main_account()
    {
             return $this->belongsTo('App\Models\Account','super_id');
    }

    public function sub_accounts()
    {
             return $this->hasMany('App\Models\Account','super_id','id')->orderBy('code');
    }

    public function sub_account()
    {
             return $this->belongsTo('App\Models\Account','super_id');
    }

    public function sub_sub_accounts()
    {
             return $this->hasMany('App\Models\Account','super_id')->orderBy('code');
    }

    public function sub_sub_account()
    {
             return $this->belongsTo('App\Models\Account','super_id');
    }

    public function detail_accounts()
    {
             return $this->hasMany('App\Models\Account','super_id')->orderBy('code');
    }

    public function account_type()
    {
        return $this->belongsTo('App\Models\Configuration','acc_type');
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class,'account_voucher','account_id','voucher_id')->withPivot('account_voucherable_id','account_voucherable_type','remarks','cheque_no','cheque_date','debit','credit')->withTimestamps();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class,'account_id','id');
    }

    public function semi_customer()
    {
        return $this->hasOne(Customer::class,'mid_sale_account_id','id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class,'account_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }



    public function trail_balance($from,$to) //trail balance of from -1 day (from not include)
    {         

         // $opening_balance=$this->opening_balance;
         // $opening_debit=0;
         // $opening_credit=0;
           
         //   if($opening_balance > 0)
         //        $opening_debit=$opening_balance;
         //      elseif($opening_balance < 0)
         //        $opening_credit=$opening_balance * -1;




      $debit=Transection::where('account_id',$this->id)->where(function($q) use($from,$to){
                         $q->where('account_voucherable_type','App\Models\Voucher')
                            ->whereHas('voucher', function($q) use($from,$to){
                              
                              
                           if($from!='')
                          { $q->where('voucher_date', '>=', $from); }
                            if($to!='')
                             { $q->where('voucher_date', '<=', $to); }
                    
                
                              })
                            ->orWhere('account_voucherable_type','App\Models\Sale')
                            ->whereHas('sale', function($q) use($from,$to){

                          $q->orderBy('invoice_date', 'asc');
                    if($from!='')
                    { $q->where('invoice_date', '>=', $from); }
                   if($to!='')
                   { $q->where('invoice_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Deliverychallan')
                            ->whereHas('challan', function($q) use($from,$to){

                          $q->orderBy('challan_date', 'asc');
                    if($from!='')
                    { $q->where('challan_date', '>=', $from); }
                   if($to!='')
                   { $q->where('challan_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\yield_detail')
                            ->whereHas('transfer_note', function($q) use($from,$to){

                          $q->orderBy('transfer_date', 'asc');
                    if($from!='')
                    { $q->where('transfer_date', '>=', $from); }
                   if($to!='')
                   { $q->where('transfer_date', '<=', $to); }
                    
                
                              })


                            ->orWhere('account_voucherable_type','App\Models\Purchase')
                            ->whereHas('purchase', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Issuance')
                            ->whereHas('issuance', function($q) use($from,$to){

                          $q->orderBy('issuance_date', 'asc');
                    if($from!='')
                    { $q->where('issuance_date', '>=', $from); }
                   if($to!='')
                   { $q->where('issuance_date', '<=', $to); }
                
                              })


                            ->orWhere('account_voucherable_type','App\Models\issue_return')
                            ->whereHas('issue_return', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                
                              })


                            ->orWhere('account_voucherable_type','App\Models\Purchasereturn')
                            ->whereHas('purchasereturn', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salereturn')
                            ->whereHas('salereturn', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\stock_transfer')
                            ->whereHas('stock_transfer', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salary')
                            ->whereHas('salary', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                   if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                             

                            ;

                          //   if($from=='')
                          // $q->orWhere('account_voucherable_type','App\Models\inventory');

                })->sum('debit');

            


            $credit=Transection::where('account_id',$this->id)->where(function($q) use($from,$to){
                         $q->where('account_voucherable_type','App\Models\Voucher')
                            ->whereHas('voucher', function($q) use($from,$to){
                              
                              
                           if($from!='')
                          { $q->where('voucher_date', '>=', $from); }
                            if($to!='')
                             { $q->where('voucher_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Sale')
                            ->whereHas('sale', function($q) use($from,$to){

                          $q->orderBy('invoice_date', 'asc');
                    if($from!='')
                    { $q->where('invoice_date', '>=', $from); }
                   if($to!='')
                   { $q->where('invoice_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Deliverychallan')
                            ->whereHas('challan', function($q) use($from,$to){

                          $q->orderBy('challan_date', 'asc');
                    if($from!='')
                    { $q->where('challan_date', '>=', $from); }
                   if($to!='')
                   { $q->where('challan_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\yield_detail')
                            ->whereHas('transfer_note', function($q) use($from,$to){

                          $q->orderBy('transfer_date', 'asc');
                    if($from!='')
                    { $q->where('transfer_date', '>=', $from); }
                   if($to!='')
                   { $q->where('transfer_date', '<=', $to); }
                    
                
                              })


                            ->orWhere('account_voucherable_type','App\Models\Purchase')
                            ->whereHas('purchase', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                             ->orWhere('account_voucherable_type','App\Models\Issuance')
                            ->whereHas('issuance', function($q) use($from,$to){

                          $q->orderBy('issuance_date', 'asc');
                    if($from!='')
                    { $q->where('issuance_date', '>=', $from); }
                   if($to!='')
                   { $q->where('issuance_date', '<=', $to); }
                    
                
                              })


                            ->orWhere('account_voucherable_type','App\Models\issue_return')
                            ->whereHas('issue_return', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Purchasereturn')
                            ->whereHas('purchasereturn', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salereturn')
                            ->whereHas('salereturn', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\stock_transfer')
                            ->whereHas('stock_transfer', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salary')
                            ->whereHas('salary', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })


                            ;

                          //   if($from=='')
                          // $q->orWhere('account_voucherable_type','App\Models\inventory');



                })->sum('credit');

               //$debit =$debit + $opening_debit;
               //$credit  = $credit + $opening_credit;

              $accs=$this->sub_accounts;
               foreach ($accs as $acc) {
                
                $t=$acc->trail_balance($from,$to);
                $debit = $debit + $t['debit'];
                $credit = $credit + $t['credit'];
                //print_r($balance.' ');
            }
           

           return ['debit'=>$debit, 'credit'=>$credit];
    }


     public function balance($to) // balance of from -1 day (from not include)
    {         

         $opening_balance=$this->opening_balance;
         //$opening_balance=0;

         // $accs=$this->sub_accounts;
         //       foreach ($accs as $acc) {
                
         //        $opening_balance = $opening_balance + $acc->opening_balance;
                
         //    }

         $opening_debit=0;
         $opening_credit=0;
           
           if($opening_balance > 0)
                $opening_debit=$opening_balance;
              elseif($opening_balance < 0)
                $opening_credit=$opening_balance * -1;




      $debit=Transection::where('account_id',$this->id)->where(function($q) use($to){
                         $q->where('account_voucherable_type','App\Models\Voucher')
                            ->whereHas('voucher', function($q) use($to){
                              
                          
                            if($to!='')
                             { $q->where('voucher_date', '<=', $to); }
                    
                
                              })
                            ->orWhere('account_voucherable_type','App\Models\Sale')
                            ->whereHas('sale', function($q) use($to){

                          $q->orderBy('invoice_date', 'asc');
                  
                   if($to!='')
                   { $q->where('invoice_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Deliverychallan')
                            ->whereHas('challan', function($q) use($to){

                          $q->orderBy('challan_date', 'asc');
                
                   if($to!='')
                   { $q->where('challan_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\yield_detail')
                            ->whereHas('transfer_note', function($q) use($to){

                          $q->orderBy('transfer_date', 'asc');
                   if($to!='')
                   { $q->where('transfer_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Purchase')
                            ->whereHas('purchase', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Purchasereturn')
                            ->whereHas('purchasereturn', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salereturn')
                            ->whereHas('salereturn', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
      
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\stock_transfer')
                            ->whereHas('stock_transfer', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Issuance')
                            ->whereHas('issuance', function($q) use($to){

                          $q->orderBy('issuance_date', 'asc');
                  
                   if($to!='')
                   { $q->where('issuance_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\issue_return')
                            ->whereHas('issue_return', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                  
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salary')
                            ->whereHas('salary', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                             

                            ;

                             //if($from=='') 
                           $q->orWhere('account_voucherable_type','App\Models\inventory');

                })->sum('debit');



            $credit=Transection::where('account_id',$this->id)->where(function($q) use($to){
                         $q->where('account_voucherable_type','App\Models\Voucher')
                            ->whereHas('voucher', function($q) use($to){
                              
                              
                                  if($to!='')
                             { $q->where('voucher_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Sale')
                            ->whereHas('sale', function($q) use($to){

                          $q->orderBy('invoice_date', 'asc');
                
                   if($to!='')
                   { $q->where('invoice_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Deliverychallan')
                            ->whereHas('challan', function($q) use($to){

                          $q->orderBy('challan_date', 'asc');
                
                   if($to!='')
                   { $q->where('challan_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\yield_detail')
                            ->whereHas('transfer_note', function($q) use($to){

                          $q->orderBy('transfer_date', 'asc');
                   if($to!='')
                   { $q->where('transfer_date', '<=', $to); }
                    
                
                              })


                            ->orWhere('account_voucherable_type','App\Models\Purchase')
                            ->whereHas('purchase', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
          
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Purchasereturn')
                            ->whereHas('purchasereturn', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                    
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salereturn')
                            ->whereHas('salereturn', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
              
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\stock_transfer')
                            ->whereHas('stock_transfer', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Issuance')
                            ->whereHas('issuance', function($q) use($to){

                          $q->orderBy('issuance_date', 'asc');
                
                   if($to!='')
                   { $q->where('issuance_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\issue_return')
                            ->whereHas('issue_return', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salary')
                            ->whereHas('salary', function($q) use($to){

                          $q->orderBy('doc_date', 'asc');
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })


                            ;

                           // if($from=='')
                          $q->orWhere('account_voucherable_type','App\Models\inventory');



                })->sum('credit');

                 $debit =$debit + $opening_debit;
               $credit  = $credit + $opening_credit;

               $balance=$debit - $credit;
                   
                   //print_r(json_encode($balance).' , ');

                $accs=$this->sub_accounts;
               foreach ($accs as $acc) {
                
                $balance = $balance + $acc->balance($to);
                //print_r($balance.' ');
            }
          
           return $balance;
    }

    

   
    
    

    public function opening_balance($from="")//opening balance of account
    {
         $opening_balance=0;

        if($from=='')
         {
             $opening_balance=$this->opening_balance;

              $accs=$this->sub_accounts;
               foreach ($accs as $acc) {
                
                $opening_balance = $opening_balance +  $acc->opening_balance;
               
            }

              return $opening_balance;
           }


           $to=date('Y-m-d', strtotime($from. ' -1 days'));
        
            
             // $trail=$this->trail_balance('',$to);
             // $opening_balance  = $trail['debit'] - $trail['credit'] ;
            
                $opening_balance=$this->closing_balance($to);

                  //die;
            return $opening_balance;

    }

     public function closing_balance($to)//closing balance of account
    {
         $closing_balance=0;
           
           
             // $trail=$this->trail_balance('',$to);
              //$closing_balance  = $trail['debit'] - $trail['credit'] ;
            
            $closing_balance=$this->balance($to);

            //$accs=$this->sub_accounts;

            //foreach ($accs as $acc) {
                
                //$closing_balance = $closing_balance + $acc->closing_balance($to);
            //}

            return $closing_balance;

    }


    public function ledger($from,$to,$type)//ledger of account
    {    
           $opening=$this->opening_balance($from);
           $closing=$this->closing_balance($to);
           //$opening=0;
           //$closing=0;

         //print_r($opening);die;
           $all_transections=[];

           if($type==1)
           {

           
                $all_transections=Transection::where('account_id',$this->id)->where(function($q) use($from,$to){

                         $q->where('account_voucherable_type','App\Models\Voucher')
                            ->whereHas('voucher', function($q) use($from,$to){
                              
                           if($from!='')
                          { $q->where('voucher_date', '>=', $from); }
                            if($to!='')
                             { $q->where('voucher_date', '<=', $to); }
                    
                
                              })

                   //          ->orWhere('account_voucherable_type','App\Models\Asset')
                   //          ->where( function($q) use($from,$to){

                   //        $q->orderBy('purchase_date', 'asc');
                   //  if($from!='')
                   //  { $q->where('purchase_date', '>=', $from); }
                   // if($to!='')
                   // { $q->where('purchase_date', '<=', $to); }
                    
                
                   //            })


                            ->orWhere('account_voucherable_type','App\Models\Sale')
                            ->whereHas('sale', function($q) use($from,$to){

                          $q->orderBy('invoice_date', 'asc');
                    if($from!='')
                    { $q->where('invoice_date', '>=', $from); }
                   if($to!='')
                   { $q->where('invoice_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Deliverychallan')
                            ->whereHas('challan', function($q) use($from,$to){

                          $q->orderBy('challan_date', 'asc');
                    if($from!='')
                    { $q->where('challan_date', '>=', $from); }
                   if($to!='')
                   { $q->where('challan_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\yield_detail')
                            ->whereHas('transfer_note', function($q) use($from,$to){

                          $q->orderBy('transfer_date', 'asc');
                    if($from!='')
                    { $q->where('transfer_date', '>=', $from); }
                   if($to!='')
                   { $q->where('transfer_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Purchase')
                            ->whereHas('purchase', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Purchasereturn')
                            ->whereHas('purchasereturn', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salereturn')
                            ->whereHas('salereturn', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\stock_transfer')
                            ->whereHas('stock_transfer', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                             ->orWhere('account_voucherable_type','App\Models\Issuance')
                            ->whereHas('issuance', function($q) use($from,$to){

                          $q->orderBy('issuance_date', 'asc');
                    if($from!='')
                    { $q->where('issuance_date', '>=', $from); }
                   if($to!='')
                   { $q->where('issuance_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\issue_return')
                            ->whereHas('issue_return', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salary')
                            ->whereHas('salary', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })


                                        

                            ;
                          if($from=='')
                          $q->orWhere('account_voucherable_type','App\Models\inventory');

                })->get();

                }
           elseif($type==0)
           {

           

                //for summary ledger

                 $all_transections=Transection::with('account_voucherable')->where('account_id',$this->id)->selectRaw('sum(debit) as debit,sum(credit) as credit, account_id,account_voucherable_id,account_voucherable_type, remarks,cheque_no')->groupBy('account_voucherable_id','account_voucherable_type')->where(function($q) use($from,$to){

                         $q->where('account_voucherable_type','App\Models\Voucher')
                            ->whereHas('voucher', function($q) use($from,$to){
                              
                           if($from!='')
                          { $q->where('voucher_date', '>=', $from); }
                            if($to!='')
                             { $q->where('voucher_date', '<=', $to); }
                    
                
                              })

                   //          ->orWhere('account_voucherable_type','App\Models\Asset')
                   //          ->where( function($q) use($from,$to){

                   //        $q->orderBy('purchase_date', 'asc');
                   //  if($from!='')
                   //  { $q->where('purchase_date', '>=', $from); }
                   // if($to!='')
                   // { $q->where('purchase_date', '<=', $to); }
                    
                
                             // })

                            ->orWhere('account_voucherable_type','App\Models\Sale')
                            ->whereHas('sale', function($q) use($from,$to){

                          $q->orderBy('invoice_date', 'asc');
                    if($from!='')
                    { $q->where('invoice_date', '>=', $from); }
                   if($to!='')
                   { $q->where('invoice_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Deliverychallan')
                            ->whereHas('challan', function($q) use($from,$to){

                          $q->orderBy('challan_date', 'asc');
                    if($from!='')
                    { $q->where('challan_date', '>=', $from); }
                   if($to!='')
                   { $q->where('challan_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\yield_detail')
                            ->whereHas('transfer_note', function($q) use($from,$to){

                          $q->orderBy('transfer_date', 'asc');
                    if($from!='')
                    { $q->where('transfer_date', '>=', $from); }
                   if($to!='')
                   { $q->where('transfer_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Purchase')
                            ->whereHas('purchase', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Purchasereturn')
                            ->whereHas('purchasereturn', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\Salereturn')
                            ->whereHas('salereturn', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                            ->orWhere('account_voucherable_type','App\Models\stock_transfer')
                            ->whereHas('stock_transfer', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })

                             ->orWhere('account_voucherable_type','App\Models\Issuance')
                            ->whereHas('issuance', function($q) use($from,$to){

                          $q->orderBy('issuance_date', 'asc');
                    if($from!='')
                    { $q->where('issuance_date', '>=', $from); }
                   if($to!='')
                   { $q->where('issuance_date', '<=', $to); }
                    
                
                              })

                             ->orWhere('account_voucherable_type','App\Models\issue_return')
                            ->whereHas('issue_return', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })


                            ->orWhere('account_voucherable_type','App\Models\Salary')
                            ->whereHas('salary', function($q) use($from,$to){

                          $q->orderBy('doc_date', 'asc');
                    if($from!='')
                    { $q->where('doc_date', '>=', $from); }
                   if($to!='')
                   { $q->where('doc_date', '<=', $to); }
                    
                
                              })



                                        

                            ;
                          if($from=='')
                          $q->orWhere('account_voucherable_type','App\Models\inventory');

                })->get();


                 }

                
               
               $transections=array();
               foreach ($all_transections as $all ) {

                $date='';
                if($all['account_voucherable_type']=='App\Models\Sale')
                  $date=$all['account_voucherable']['invoice_date'];
                elseif($all['account_voucherable_type']=='App\Models\Deliverychallan')
                  $date=$all['account_voucherable']['challan_date'];
                elseif($all['account_voucherable_type']=='App\Models\Voucher')
                  $date=$all['account_voucherable']['voucher_date'];
                elseif($all['account_voucherable_type']=='App\Models\Purchase')
                  $date=$all['account_voucherable']['doc_date'];
                elseif($all['account_voucherable_type']=='App\Models\Purchasereturn')
                  $date=$all['account_voucherable']['doc_date'];
                elseif($all['account_voucherable_type']=='App\Models\Salereturn')
                  $date=$all['account_voucherable']['doc_date'];
                elseif($all['account_voucherable_type']=='App\Models\stock_transfer')
                  $date=$all['account_voucherable']['doc_date'];
                elseif($all['account_voucherable_type']=='App\Models\Salary')
                  $date=$all['account_voucherable']['doc_date'];
                elseif($all['account_voucherable_type']=='App\Models\Issuance')
                  $date=$all['account_voucherable']['issuance_date'];
                elseif($all['account_voucherable_type']=='App\Models\issue_return')
                  $date=$all['account_voucherable']['doc_date'];
                elseif($all['account_voucherable_type']=='App\Models\yield_detail')
                  $date=$all['account_voucherable']['transfer_date'];


                $voucher_no='';
                if($all['account_voucherable_type']=='App\Models\Sale')
                  $voucher_no=$all['account_voucherable']['invoice_no'];
                elseif($all['account_voucherable_type']=='App\Models\Deliverychallan')
                  $voucher_no=$all['account_voucherable']['doc_no'];
                elseif($all['account_voucherable_type']=='App\Models\Voucher')
                  $voucher_no=$all['account_voucherable']['voucher_no'];
                elseif($all['account_voucherable_type']=='App\Models\Purchase')
                  $voucher_no=$all['account_voucherable']['doc_no'];
                elseif($all['account_voucherable_type']=='App\Models\Purchasereturn')
                  $voucher_no=$all['account_voucherable']['doc_no'];
                elseif($all['account_voucherable_type']=='App\Models\Salereturn')
                  $voucher_no=$all['account_voucherable']['doc_no'];
                elseif($all['account_voucherable_type']=='App\Models\stock_transfer')
                  $voucher_no=$all['account_voucherable']['doc_no'];
                elseif($all['account_voucherable_type']=='App\Models\Salary')
                  $voucher_no=$all['account_voucherable']['doc_no'];
                elseif($all['account_voucherable_type']=='App\Models\Issuance')
                  $voucher_no=$all['account_voucherable']['issuance_no'];
                elseif($all['account_voucherable_type']=='App\Models\issue_return')
                  $voucher_no=$all['account_voucherable']['doc_no'];
                elseif($all['account_voucherable_type']=='App\Models\yield_detail')
                  $voucher_no=$all['account_voucherable']['yield']['plan']['plan_no'];

                $link='';
                if($all['account_voucherable_type']=='App\Models\Sale')
                  $link='edit/sale/'.$all['account_voucherable']['id'];
                elseif($all['account_voucherable_type']=='App\Models\Deliverychallan')
                  $link='edit/delivery-challan/'.$all['account_voucherable']['id'];
                elseif($all['account_voucherable_type']=='App\Models\Voucher')
                  {
                    if($all['account_voucherable']['category']=='voucher')
                    $link='edit/voucher/'.$all['account_voucherable']['id'];
                     elseif($all['account_voucherable']['category']=='payment')
                    $link='edit/payment/'.$all['account_voucherable']['id'];
                     elseif($all['account_voucherable']['category']=='receipt')
                    $link='edit/receipt/'.$all['account_voucherable']['id'];
                  elseif($all['account_voucherable']['category']=='expense')
                    $link='edit/expense/'.$all['account_voucherable']['id'];
                  }
                  elseif($all['account_voucherable_type']=='App\Models\Purchase')
                     $link='edit/purchase/'.$all['account_voucherable']['id'];
                   elseif($all['account_voucherable_type']=='App\Models\Purchasereturn')
                     $link='edit/purchase/return/'.$all['account_voucherable']['id'];
                   elseif($all['account_voucherable_type']=='App\Models\Salereturn')
                     $link='edit/sale/return/'.$all['account_voucherable']['id'];
                   elseif($all['account_voucherable_type']=='App\Models\stock_transfer')
                     $link='edit/stock/transfer/'.$all['account_voucherable']['id'];
                   elseif($all['account_voucherable_type']=='App\Models\Salary')
                     $link='edit/salary/'.$all['account_voucherable']['id'];
                   elseif($all['account_voucherable_type']=='App\Models\Issuance')
                     $link='edit/issuance/'.$all['account_voucherable']['issuance_no'];
                   elseif($all['account_voucherable_type']=='App\Models\issue_return')
                     $link='edit/issuance/'.$all['account_voucherable']['id'];
                   elseif($all['account_voucherable_type']=='App\Models\yield_detail')
                     $link='edit/transfer-note/'.$all['account_voucherable']['yield']['id'];
                  else
                    $link='#';

                 $remarks='';
                 if($type==0)
                 {
                    if($all['account_voucherable_type']=='App\Models\Sale')
                  $remarks='Sale Invoice';
                elseif($all['account_voucherable_type']=='App\Models\Deliverychallan')
                  $remarks='Delivery Challan';
                elseif($all['account_voucherable_type']=='App\Models\Voucher')
                  {
                    if($all['account_voucherable']['category']=='voucher')
                       $remarks='Voucher';
                     elseif($all['account_voucherable']['category']=='payment')
                       $remarks='Payment Voucher';
                     elseif($all['account_voucherable']['category']=='receipt')
                       $remarks='Receipt Voucher';
                  elseif($all['account_voucherable']['category']=='expense')
                       $remarks='Expense Voucher';
                  }
                  elseif($all['account_voucherable_type']=='App\Models\Purchase')
                     $remarks='Purchase Invoice';
                   elseif($all['account_voucherable_type']=='App\Models\Purchasereturn')
                     $remarks='Purchase Return';
                   elseif($all['account_voucherable_type']=='App\Models\Salereturn')
                     $remarks='Sale Return';
                   elseif($all['account_voucherable_type']=='App\Models\stock_transfer')
                     $remarks='Stock Transfer';
                   elseif($all['account_voucherable_type']=='App\Models\Salary')
                     $remarks='Salry :'.$all['account_voucherable']['month'];
                   elseif($all['account_voucherable_type']=='App\Models\Issuance')
                     $remarks='Issuance :'.$all['account_voucherable']['issuance_no'];
                   elseif($all['account_voucherable_type']=='App\Models\issue_return')
                     $remarks='Issuance Return:'.$all['account_voucherable']['doc_no'];
                   elseif($all['account_voucherable_type']=='App\Models\yield_detail')
                     $remarks=$all['account_voucherable']['remarks'];
                  else
                    $remarks='#';
                }
                else
                {
                  $remarks=$all['remarks'];
                }
                

                 $trans=array('date'=>$date,'link'=>$link,'voucher_no'=>$voucher_no,'remarks'=>$remarks,'cheque_no'=>$all['cheque_no'],'debit'=>$all['debit'],'credit'=>$all['credit']);

                 array_push($transections, $trans);
               }

                $sort_col  = array_column($transections, 'date');
           array_multisort($sort_col,SORT_ASC,$transections);


           

           return [  'transections'=>$transections, 'opening'=>$opening, 'closing'=>$closing ];
    }


}
