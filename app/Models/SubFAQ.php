<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SubFAQ extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['faq_id', 'question', 'answer'];

    public function faq()
    {
        return $this->belongsTo(FAQ::class, 'faq_id');
    }
}
