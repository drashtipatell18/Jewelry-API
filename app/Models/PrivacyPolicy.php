<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PrivacyPolicy extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "privacy_policies";
    protected $fillable = ['title','description'];
}
