<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','item_type','item_id','label','amount','ticket_number'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
