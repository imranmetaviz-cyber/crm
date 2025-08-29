<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salereturn extends Model
{
    use HasFactory;

     


    public function items()
    {
        return $this->belongsToMany(inventory::class,'sale_return_item','return_id','item_id')->withPivot('id','sale_stock_id','unit','qty','pack_size','mrp','business_type','batch_no','expiry_date','discount_type','discount_factor','tax')->withTimestamps();
    }

    public function return_list()
    {
        return $this->hasMany('App\Models\salereturn_ledger','return_id','id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function transections()
    {
        return $this->morphMany(Transection::class, 'account_voucherable');
    }

    public function total_quantity()
    {
        $total = 0;
        foreach ($this->items as $item ) {
            
            $total += $item['pivot']['qty'] * $item['pivot']['pack_size'] ;
        }

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



     public function rate($item_id,$pivot_id)
    {
              $item=$this->items->where('id',$item_id)->where('pivot.id',$pivot_id)->first();
              $item_count=count( $this->items );

              $total_qty= $item['pivot']['qty'] * $item['pivot']['pack_size'] ; 

            
              $tp=0.85 * $item['pivot']['mrp'];
               $d=0;

               if($item['pivot']['discount_type']=='flat')
                 $d=$item['pivot']['discount_factor'];
                if($item['pivot']['discount_type']=='percentage')
                $d=($item['pivot']['discount_factor'] / 100) *  $tp;
              
              $rate=$tp - $d;
                  

                  $tax=$item['pivot']['tax'];
                $t=($tax / 100) * $rate;
                $rate=$rate  + $t;

                $amount=$rate * $total_qty;

                // if($this->net_discount_type=='flat')
                //     $net_discount=$this->net_discount / $item_count;
                // elseif($this->net_discount_type=='percentage')
                //       $net_discount=($this->net_discount /100)*$rate;
              
                //   $rate = $rate- $net_discount;
                                      
                       $rate=round( $rate ,2 );

                   return $rate;

    }

}
