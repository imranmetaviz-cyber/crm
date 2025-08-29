<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sale_stock extends Model
{
    use HasFactory;
    protected $table = 'item_sale';

    protected $appends = ['discount','total_qty','amount'];

    public function item()
    {

        return $this->belongsTo('App\Models\inventory','item_id');
    }

      public function sale()
    {

        return $this->belongsTo('App\Models\Sale','invoice_id');
    }



    public function getTotalQtyAttribute()
    {
      return $this->total_qty();
    }

    public function total_qty()
    {
           
        return $this->qty * $this->pack_size;
    }

    public function tp()
    {
            $tp=round( 0.85 * $this->mrp ,2);
        return $tp;
    }

    // public function getRateAttribute()
    // {
    //   return $this->rate();
    // }

    public function getDiscountAttribute()
    {
      return $this->discount();
    }

    public function discount()
    {

        // $mrp=$this->mrp;
        //    if($mrp=='' || $mrp==null)
        //     $mrp=0;
        //    $tp = round( 0.85 * $mrp ,2);
      $rate=$this->rate;
      if($rate=='' || $rate==null)
        $rate=0;

           $discount_type=$this->discount_type ; 
           $discount_factor=$this->discount_factor;
           if($discount_factor=='' || $discount_factor==null)
            $discount_factor=0;
            
            $discount=0;

           if($discount_type=='percentage')
           {
              $discount=($discount_factor/100) * $rate ;
           }
           elseif($discount_type=='flat') 
           {
              $discount=$discount_factor;
           }

           return $discount;
    }

    public function rate()
    {  

           //$d_rate=0;
           // $mrp=$this->mrp;
           // if($mrp=='' || $mrp==null)
           //  $mrp=0;
           // $tp = round( 0.85 * $mrp ,2);
           // $discount_type=$this->discount_type ; 
           // $discount_factor=$this->discount_factor;
           // if($discount_factor=='' || $discount_factor==null)
           //  $discount_factor=0;
           
           $discount=$this->discount();
           // if($discount_type=='percentage')
           // {
           //    $discount=($discount_factor/100) * $tp ;
           //    $rate=$tp-$discount;
           // }
           // elseif($discount_type=='flat') 
           // {
           //    $rate=$tp-$discount_factor;
           // }

           $rate=$this->rate-$discount;

          
           $rate=round($rate,2);

        

           // $net_discount=$this->sale->net_discount ; 
           // $net_discount_type=$this->sale->net_discount_type;
           // if($net_discount=='' || $net_discount==null)
           //  $net_discount=0;

           // if($net_discount_type=='percentage')
           // {
           //    $discount=($net_discount/100) * $rate ;
           //    $rate=$rate-$discount;
           // }
           // elseif($net_discount_type=='flat') 
           // {
           //    $rate=$rate-$net_discount;
           // }

           // $rate=round($rate,2);

        return $rate;
    }

    public function getAmountAttribute()
    {
      return  $this->total_qty * $this->rate ;
    }

    public function commission()
    {
          //$item=$this->items->where('pivot.id',$pivot_id)->first();
          $rate=$this->rate();

          $v=0;

               if($this['commission_type']=='flat')
                 $v=$this['commission_factor'];
                if($this['commission_type']=='percentage')
                $v=($this['commission_factor'] / 100) *  $rate;
              
              $amount= $v;
               
                $amount=$amount * $this->total_qty;

            return $amount;
    }




}
