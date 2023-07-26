<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'review',
        'user_id',
        'business_id',
    ];
     public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
     {
         return $this->belongsTo(User::class);
     }
    public function business(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
