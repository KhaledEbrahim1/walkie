<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'location',
        'city',
        'images'
    ];
    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
