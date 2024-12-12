<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_name',
        'category_id',
        'sub_category_id',
        'metal_color',
        'metal',
        'diamond_color',
        'diamond_quality',
        'image',
        'clarity',
        'size_id',
        'size_name',
        'weight',
        'status',
        'no_of_diamonds',
        'diamond_setting',
        'diamond_shape',
        'collection',
        'gender',
        'description',
        'qty',
        'price',
        'discount',
    'sku'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
