<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "orders";
    protected $fillable = ['customer_id','deliveryAddress_id', 'stock_id', 'order_date','total_amount','order_status','invoice_number','qty'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function deliveryAddress()
    {
        return $this->belongsTo(DeliveryAddress::class, 'deliveryAddress_id');
    }
}
