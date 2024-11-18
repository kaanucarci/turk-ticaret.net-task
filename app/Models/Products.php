<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Products extends Model
{

    /**
     * Ürün ile sepetler arasındaki ilişkiyi tanımlar.
     */
    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Carts::class, 'cart_items', 'product_id', 'cart_id')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
