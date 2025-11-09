<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'country',
        'address',
        'postal_code',
        'description',
        'total_price',
        'status',
    ];

    // 🧩 Marrëdhënia me produktet e porosisë
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
