<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public function sub_menu_items()
    {
        return $this->hasMany(Menu::class,'super_id')->orderBY('sort_order','asc');  
    }


}
