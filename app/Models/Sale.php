<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->belongsToMany(inventory::class,'item_sale','invoice_id','item_id')->withPivot('id','unit','qty','pack_size','mrp','batch_no','expiry_date','rate','discount_type','discount_factor','commission_type','commission_factor')->withTimestamps();
    }

    public function sale_stock_list()
    {
        return $this->hasMany('App\Models\sale_stock','invoice_id','id');
    }

    public function salesman()
    {
        return $this->belongsTo('App\Models\Employee','salesman_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

     public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    public function shipment_port()
    {
        return $this->belongsTo('App\Models\Port','shipment_port_id');
    }

    public function discharge_port()
    {
        return $this->belongsTo('App\Models\Port','discharge_port_id');
    }

    public function packing_type()
    {
        return $this->belongsTo('App\Models\packing_type','packing_type_id');
    }

    public function freight_type()
    {
        return $this->belongsTo('App\Models\freight_type','freight_type_id');
    }

    public function transportation()
    {
        return $this->belongsTo('App\Models\Transportation','transportation_id');
    }

    public function expenses()
    {
        return $this->belongsToMany(Expense::class,'sale_expense','sale_id','expense_id')->withPivot('amount')->withTimestamps();
    }

    //public function salereturn()
    //{
        //return $this->hasOne('App\Models\Salereturn','invoice_id');
    //}

    public function challan()
    {
        return $this->belongsTo('App\Models\Deliverychallan');
    }

    public function total_quantity()
    {
        $total = 0;
        foreach ($this->items as $item ) {
            
            $total += $item['pivot']['qty'] * $item['pivot']['pack_size'] ;
        }

        return $total ;
    }

    public function amount_ex_discount()
    {
        $total = 0;

        foreach ($this->items as $item ) {
            
            $qty = $item['pivot']['qty'] * $item['pivot']['pack_size'] ;
              $rate=$this->rate($item['id'],$item['pivot']['id']);

              $amount=$qty  * $rate ;
              $total= $total + $amount;

        }

        return $total ;
    }

    public function net_discount()
    {
        $total = $this->amount_ex_discount();
        $discount=0;

        if($this->net_discount==null || $this->net_discount==0 || $this->net_discount=='')
            return $discount;
        $type=$this->net_discount_type;

          if($type=='flat')
              $discount=$this->net_discount;
            elseif($type=='percentage')
            {
                $discount=round( ($this->net_discount / 100 )*$total ,2 );
            }
    
        return $discount ;
    }

    public function amount_in_discount()
    {
        $total = $this->amount_ex_discount();
        $discount=$this->net_discount();
          $rem=$total-$discount;

          return $rem;
    }

    public function total_amount()
    {
        

        $amount=$this->amount_in_discount();
        $gst=$this->gst_amount();
         $exp=$this->expense_amount();
        $total=$amount+$gst+$exp;

        return $total ;
    }


    public function item_names()
    {
        $names = '';

        $i=1; $count= count($this->items);
        foreach ($this->items as $item ) {
            
            $names  = $names . $item['item_name'] ;
             
             if( $i!= $count )
               $names  = $names . ' , ' ;

            $i++;
        }

        return $names ;
    }

    public function transections()
    {
        return $this->morphMany(Transection::class, 'account_voucherable');
    }

    

    public function rate($item_id,$pivot_id)
    {
        $item=$this->items->where('id',$item_id)->where('pivot.id',$pivot_id)->first();
        
              //$item_count=count( $this->items );

              //$total_qty= $item['pivot']['qty'] * $item['pivot']['pack_size'] ; 

            
              // $tp=0.85 * $item['pivot']['mrp'];
              //  $d=0;

               if($item['pivot']['discount_type']=='flat')
                 $d=$item['pivot']['discount_factor'];
                if($item['pivot']['discount_type']=='percentage')
                $d=($item['pivot']['discount_factor'] / 100) *  $item['pivot']['rate'];
              
              $rate=$item['pivot']['rate'] - $d;
                  

                 

                //$amount=$rate * $total_qty;

                // if($this->net_discount_type=='flat')
                //     $net_discount=$this->net_discount / $item_count;
                // elseif($this->net_discount_type=='percentage')
                //       $net_discount=($this->net_discount /100)*$rate;
              
                //   $rate = $rate- $net_discount;
                                      
                       $rate=round( $rate ,2 );

                   return $rate;

    }

    public function commission($pivot_id)
    {
          $item=$this->items->where('pivot.id',$pivot_id)->first();
          $rate=$this->rate($item['id'],$pivot_id);

          $v=0;

               if($item['pivot']['commission_type']=='flat')
                 $v=$item['pivot']['commission_factor'];
                if($item['pivot']['commission_type']=='percentage')
                $v=($item['pivot']['commission_factor'] / 100) *  $rate;
              
              $amount= $v;
              $total_qty= $item['pivot']['qty'] * $item['pivot']['pack_size'] ; 
                $amount=$amount * $total_qty;

            return $amount;
    }

    public function gst_amount()
    {
          
          $amount=$this->amount_ex_discount();

         
          $amount=round( ($this->gst / 100) *  $amount ,2 );
              
              

            return $amount;
    }

    public function expense_amount()
    {
          
          $amount=0;

          foreach ($this->expenses as $key ) {
            
            $amount= $amount + $key['pivot']['amount'];
          }    

            return $amount;
    }


}
