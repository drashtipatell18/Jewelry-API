<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Offer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'type', 'image', 'button_text', 'discount', 'description', 'status', 'start_date', 'end_date'];
}
