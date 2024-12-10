<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryAddress extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "delivery_address";
    protected $fillable = ['customer_id','deliveryAddress_id', 'address','pincode','contact_name','contact_no','city','state','type','status'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'deliveryAddress_id');
    }
}
