<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ReturnOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'order_id',
        'customer_id',
        'stock_id',
        'product_id',
        'return_date',
        'return_status',
        'price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id' );
    }

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class,'stock_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
