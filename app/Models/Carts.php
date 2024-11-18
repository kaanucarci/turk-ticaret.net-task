<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Carts extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sepet ile ürünler arasındaki ilişkiyi tanımlar.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Products::class, 'cart_items', 'cart_id', 'product_id')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
