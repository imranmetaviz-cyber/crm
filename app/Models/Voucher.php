<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    public function voucher_type()
    {
        return $this->belongsTo('App\Models\Configuration','voucher_type_id');
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class,'account_voucher','voucher_id','account_id')->withPivot('id','account_voucherable_id','account_voucherable_type','remarks','cheque_no','cheque_date','debit','credit')->withTimestamps();
    }

    public function amount()
    {
        $total = 0;
        foreach ($this->accounts as $item ) {
            
            $total += $item['pivot']['credit']  ;
        }

        return $total ;
    }


    public function transections()
    {
        return $this->morphMany(Transection::class, 'account_voucherable');
    }


    


}
