<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order_Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "order_products";
    protected $fillable = ['order_id', 'product_id', 'qty', 'discount','size','metal','metal_color'];
}
