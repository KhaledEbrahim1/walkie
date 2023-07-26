<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Reel extends Model
{
    protected $fillable = [
        'reel_title',
        'reel_url',
        'user_id',
    ];
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function likes()
    {
        return $this->belongsToMany(User::class, 'user_reel_likes', 'reel_id', 'user_id')
            ->where('liked', true)
            ->withTimestamps();
    }
}
