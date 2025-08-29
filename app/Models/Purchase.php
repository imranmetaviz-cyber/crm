<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->belongsToMany(inventory::class,'inventory_purchase','purchase_id','item_id')->withPivot('id','stock_id','unit','quantity','pack_size','current_rate','pack_rate','discount','tax')->withTimestamps();
    }

    public function item_list()
    {
        return $this->hasMany(purchase_ledger::class,'purchase_id','id');
    }


    public function transections()
    {
        return $this->morphMany(Transection::class, 'account_voucherable');
    }


    public function expenses()
    {
        return $this->belongsToMany(Expense::class,'purchase_expense','purchase_id','expense_id')->withPivot('amount')->withTimestamps();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor');
    }

     public function grn()
    {
        return $this->belongsTo(Grn::class,'grn_id');
    }

    public function purchasereturn()
    {
        return $this->hasOne('App\Models\Purchasereturn','purchase_id');
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

    public function expenses_amount()
    {
        $amount=0;

                   foreach ($this->expenses as $exp ) {
                       
                  $amount  = $amount + $exp['pivot']['amount']    ;
                   }

                   
                     
                       $amount=round( $amount ,2 );

                   return $amount;

    }

    public function net_amount()
    {
      
          
          $gst=$this->gst_amount();
          $with=$this->with_hold_tax_amount();
          $net=$this->total_amount();
            $net=$net+$gst-$with;

      return $net;
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

    public function total_qty()
    {
      $net=0;
      foreach ($this->items as $it) {
          
        
           $total_qty= $it['pivot']['quantity'] *$it['pivot']['pack_size'] ; 

          

           $net  =$net + $total_qty ;
      }

      return $net;
    }

    public function gst_amount()
    {
      $amount=$this->total_amount();
      $tax=$this->gst_tax;

      $t= round( $tax/100 * $amount ,2);

      return $t;
      
    }
    
    public function with_hold_tax_amount()
    {
      $amount=$this->total_amount();
      $tax=$this->net_tax;

      $t= round( $tax/100 * $amount ,2);

      return $t;
      
    }



    public function rate($item_id,$pivot_id)
    {
              $item=$this->items->where('id',$item_id)->where('pivot.id',$pivot_id)->first();
              $item_count=count( $this->items );
              $total_qty= $item['pivot']['quantity'] *$item['pivot']['pack_size'] ; 
                  
                     
                 $rate=$this->rate_inclusive_tax($item_id,$pivot_id);
                // $amount=$rate * $total_qty;
                  
                //    $n_d=($this->net_discount / 100) *  $amount ;

                //    $amount=round( ($amount - $n_d) ,2 );

                //    foreach ($this->expenses as $exp ) {
                       
                //       
                //    }

                //    $rate=$amount / $total_qty;
                     
                //        $rate=round( $rate ,2 );

                   return $rate;

    }


}
