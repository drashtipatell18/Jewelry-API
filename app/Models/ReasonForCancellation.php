<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReasonForCancellation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "reason_for_cancellation";
    protected $fillable = [
        'name',
    'status
    ];
}
