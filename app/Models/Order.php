<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->belongsToMany(inventory::class,'order_item','order_id','item_id')->withPivot('id','quotation_item_id','unit','qty','pack_size')->withTimestamps();
    }

    public function item_list()
    {
        return $this->hasMany('App\Models\order_item','order_id','id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function quotation()
    {
      
        return $this->belongsTo('App\Models\Quotation');

    }

    

    public function dispatch_to()
    {
        return $this->belongsTo('App\Models\Customer','dispatch_to_id','id');
    }
     public function invoice_to()
    {
        return $this->belongsTo('App\Models\Customer','invoice_to_id','id');
    }

    

    public function delivery_challans()
    {
        return $this->hasMany('App\Models\Deliverychallan','order_id');
    }

    public function total_quantity()
    {
        $total = 0;
        foreach ($this->items as $item ) {
            
            $total += $item['pivot']['qty'] * $item['pivot']['pack_size'] ;
        }

        return $total ;
    }

    public function status()
    {
         $s=0;

          if(count( $this->delivery_challans)==0)
         return 'Pending';
         else
           return 'Delivered';
    }
      public static function pending_count()
      {
        $orders=Order::doesnthave('delivery_challans')->count();
        return $orders;
      }

}
