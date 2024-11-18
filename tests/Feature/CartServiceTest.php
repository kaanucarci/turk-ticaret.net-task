<?php

namespace Feature;

use App\Models\User;
use App\Models\Products;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testAddToCartWhenProductIsNew()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $product = Products::factory()->create();

        $cartService = new CartService();

        $response = $cartService->addToCart([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
        ]);

        $this->assertDatabaseHas('carts_products', [
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100
        ]);
    }
}
