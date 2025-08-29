<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    use HasFactory;


    public function items()
    {
        return $this->belongsToMany(inventory::class,'grn_inventory','grn_id','item_id')->withPivot('id','unit','quantity','rec_quantity','pack_size','approved_qty','rej_quantity','batch_no','mfg_date','exp_date','grn_no','is_active','is_sampled')->withTimestamps();
    }

    public function stock_items()
    {
        return $this->hasMany('App\Models\Stock','grn_id');
    }

    

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor');
    }

    public function order()
    {
        return $this->belongsTo(Purchaseorder::class,'purchaseorder_id');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class,'grn_id','id');
    }

    



}
