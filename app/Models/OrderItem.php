<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    // 🧩 i përket një porosie
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🧩 i përket një produkti
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
