<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase_ledger extends Model   //it is for delivery chall items (customer store)
{
    use HasFactory;
    protected $table = 'inventory_purchase';
    protected $appends = ['total_qty','rate'];
 


    public function item()
    {

        return $this->belongsTo('App\Models\inventory','item_id');
    }

      public function invoice()
    {

        return $this->belongsTo('App\Models\Purchase','purchase_id');
    }

    public function stock()
    {

        return $this->belongsTo('App\Models\Stock','stock_id');
    }
    

    public function getTotalQtyAttribute()
    {
      return $this->total_qty();
    }

    public function total_qty()
    {
           
        return $this->quantity * $this->pack_size;
    }

    public function rate_exclusive_tax()
    {
              
                  
                  $current_rate=$this->current_rate;
                  $pack_rate=$this->pack_rate;

                  $rate=0;
                  if($current_rate!=null && $pack_rate==null)
                    $rate=$current_rate;
                  elseif($current_rate==null && $pack_rate!=null)
                    $rate=$pack_rate/$this->pack_size;
                 elseif($current_rate==null && $pack_rate==null)
                    $rate=0;

               $d=($this->discount / 100) *  $rate ;

                $rate=$rate - $d;

                
                     
                      // $rate=round( $rate ,2 );

                   return $rate;

    }

    public function rate_inclusive_tax()
    {
            
                $rate=$this->rate_exclusive_tax();

                $t=($this->tax / 100) * $rate;
                $rate=$rate  + $t;
                     
                       //$rate=round( $rate ,2 );

                   return $rate;

    }


    public function getRateAttribute()
    {
      return $this->rate_inclusive_tax();
    }
    

  



}
