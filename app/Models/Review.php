<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['customer_id', 'product_id', 'description', 'rating', 'date','like','dislike'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
     }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
