<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchasereturn extends Model
{
    use HasFactory;

    public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor');
    }

    public function items()
    {
        return $this->belongsToMany(inventory::class,'purchase_return_item','return_id','item_id')->withPivot('id','purchase_stock_id','unit','p_qty','quantity','pack_size','current_rate','pack_rate','discount','tax')->withTimestamps();
    }

    public function item_list()
    {
        return $this->hasMany('App\Models\Purchasereturn_ledger','return_id','id');
    }


    public function transections()
    {
        return $this->morphMany(Transection::class, 'account_voucherable');
    }

    public function with_hold_tax_amount()
    {
      $amount=$this->total_amount();
      $tax=$this->net_tax;

      $t= round( $tax/100 * $amount ,2);

      return $t;
      
    }

    public function gst_tax_amount()
    {
      $amount=$this->total_amount();
      $tax=$this->gst_tax;

      $t= round( $tax/100 * $amount ,2);

      return $t;
      
    }

    public function total_amount()
    {
      $net=0;
      foreach ($this->items as $it) {
          
          $rate=$this->rate($it['id'],$it['pivot']['id']);
           $total_qty= $it['pivot']['quantity'] * $it['pivot']['pack_size'] ; 

           $amount=$rate * $total_qty;

           $net  =$net + $amount ;
      }

      return $net;
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
                    $rate=$pack_rate/$item['pivot']['pack_size'];
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

                $t=($item['pivot']['tax'] / 100) * $rate;
                $rate=$rate  + $t;
                     
                       //$rate=round( $rate ,2 );

                   return $rate;

    }


    public function rate($item_id,$pivot_id)
    {
              $item=$this->items->where('id',$item_id)->where('pivot.id',$pivot_id)->first();
              $item_count=count( $this->items );
              $total_qty= $item['pivot']['quantity'] *$item['pivot']['pack_size'] ; 

              $rate=$this->rate_inclusive_tax($item_id,$pivot_id);
                  
               //    $current_rate=$item['pivot']['current_rate'];
               //    $pack_rate=$item['pivot']['pack_rate'];
               //    $rate=0;
               //    if($current_rate!=null && $pack_rate==null)
               //      $rate=$current_rate;
               //    elseif($current_rate==null && $pack_rate!=null)
               //      $rate=$pack_rate;
               //   elseif($current_rate==null && $pack_rate==null)
               //      $rate=0;

               // $d=($item['pivot']['discount'] / 100) *  $rate ;

               //  $rate=$rate - $d;

               //  $t=($item['pivot']['tax'] / 100) * $rate;
               //  $rate=$rate  + $t;
                //$amount=$rate * $total_qty;
                  
                   // $n_d=($this->net_discount / 100) *  $amount ;

                   // $amount=round( ($amount - $n_d) ,2 );

                   // foreach ($this->expenses as $exp ) {
                       
                   //     $amount  = $amount + ($exp['pivot']['debit'] / $item_count ) - ( $exp['pivot']['credit'] / $item_count ) ;
                   // }

                   //$rate=$amount / $total_qty;
                     
                       //$rate=round( $rate ,2 );

                   return $rate;

    }





    
}
