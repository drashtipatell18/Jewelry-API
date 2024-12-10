<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "orders";
    protected $fillable = ['order_number','customer_id','deliveryAddress_id', 'product_id', 'products','order_date', 'discount','total_amount','order_status','invoice_number','qty','size','metal'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function deliveryAddress()
    {
        return $this->belongsTo(DeliveryAddress::class, 'deliveryAddress_id');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
                    ->withPivot('qty')
                    ->withPivot('size')
                    ->withPivot('metal')
                    ->withPivot('discount');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            do {
                $order->order_number = mt_rand(100000, 999999); // Generate a 6-digit number
            } while (Order::where('order_number', $order->order_number)->exists());
        });
    }
}
