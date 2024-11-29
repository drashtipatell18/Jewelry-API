<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProductOffer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'product_id',
        'name',
        'code',
        'description',
        'price',
        'status',
        'start_date',
        'end_date',
        'minimum_purchase',
        'minimum_discount',
        'type',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class,'subcategory_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
