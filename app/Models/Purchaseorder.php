<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchaseorder extends Model
{
    use HasFactory;

    

    public function demand()
    {
        return $this->belongsTo(Purchasedemand::class,'purchasedemand_id');
    }

    public function items()
    {
        return $this->belongsToMany(inventory::class,'inventory_purchaseorder','order_id','item_id')->withPivot('id','unit','quantity','pack_size','current_rate','pack_rate','discount','tax')->withTimestamps();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor');
    }

    public function grn()
    {
        return $this->hasOne(Grn::class,'purchaseorder_id','id');
    }

    public function transport_via()
    {
        return $this->belongsTo('App\Models\Configuration','shipped_via','id');
    }

     public function ordered_by()
    {
        return $this->belongsTo('App\Models\Employee','order_by','id');
    }
     public function approved_by()
    {
        return $this->belongsTo('App\Models\Employee','approve_by','id');
    }

    public function expenses()
    {
        return $this->belongsToMany(Expense::class,'po_expense','order_id','expense_id')->withPivot('amount')->withTimestamps();
    }

    public function rate_exclusive_tax($item_id,$pivot_id)
    {
              $item=$this->items->where('id',$item_id)->where('pivot.id',$pivot_id)->first();
              
              $total_qty= $item['pivot']['quantity'] *$item['pivot']['pack_size'] ; 
                  
                  $current_rate=$item['pivot']['current_rate'];
                  $pack_rate=$item['pivot']['pack_rate'];
                  $rate=0;
                  if($current_rate!=null && $pack_rate==null)
                    $rate=$current_rate;
                  elseif($current_rate==null && $pack_rate!=null)
                    $rate=$pack_rate/$item['pivot']['pack_size'] ;
                 elseif($current_rate==null && $pack_rate==null)
                    $rate=0;

               $d=($item['pivot']['discount'] / 100) *  $rate ;

                $rate=$rate - $d;

                
                     
                       //$rate=round( $rate ,2 );

                   return $rate;

    }

    public function rate_inclusive_tax($item_id,$pivot_id)
    {
               $item=$this->items->where('id',$item_id)->where('pivot.id',$pivot_id)->first();
             

                $rate=$this->rate_exclusive_tax($item_id,$pivot_id);

                $t= ($item['pivot']['tax'] / 100) * $rate ;
                $rate=$rate  + $t;
                     
                       //$rate=round( $rate ,2 );

                   return $rate;

    }

    public function expenses_amount()
    {
        $amount=0;

                   foreach ($this->expenses as $exp ) {
                       
                  $amount  = $amount + $exp['pivot']['amount']    ;
                   }

                   
                     
                       $amount=round( $amount ,2 );

                   return $amount;

    }

    public function rate($item_id,$pivot_id)
    {
              $item=$this->items->where('id',$item_id)->where('pivot.id',$pivot_id)->first();
              $item_count=count( $this->items );
              $total_qty= $item['pivot']['quantity'] *$item['pivot']['pack_size'] ; 
                  
                     
                // $rate=$this->rate_inclusive_tax($item_id,$pivot_id);
                // $amount=$rate * $total_qty;
                  
                //    $n_d=($this->net_discount / 100) *  $amount ;

                //    $amount=round( ($amount - $n_d) ,2 );

                //    foreach ($this->expenses as $exp ) {
                       
                //        $amount  = $amount + ($exp['pivot']['debit'] / $item_count ) - ( $exp['pivot']['credit'] / $item_count ) ;
                //    }

                //    $rate=$amount / $total_qty;
                     
                //        $rate=round( $rate ,2 );

                   return $rate;

    }


}
