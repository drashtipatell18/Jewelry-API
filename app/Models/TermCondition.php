<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TermCondition extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "term_conditions";
    protected $fillable = ['title','description'];
}
