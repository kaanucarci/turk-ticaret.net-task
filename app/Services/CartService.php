<?php
// app/Services/CartService.php

namespace App\Services;

use App\Models\CartItems;
use App\Models\Carts;
use App\Models\Products;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function addToCart(array $cartItems)
    {
        $cart = $this->getCart();

        $existingProduct = $cart->products()->where('product_id', $cartItems['product_id'])->first();

        if ($existingProduct) {
            return $this->updateProductInCart($cart, $existingProduct, $cartItems);
        }

        return $this->addNewProductToCart($cart, $cartItems);
    }

    public function getCart()
    {
        $cart = Carts::where('user_id', Auth::user()->id)->first();

        if (!$cart || $cart->status == 'completed')
        {
            $cart = $this->createCart();
        }

        $cart_items = CartItems::where('cart_id', $cart->id)->get();

        $cart->items = $cart_items;
        $cart->total_price = $cart_items->sum('price');

        return $cart;
    }

    public function getProductInfo($productId)
    {
        $product = Products::find($productId);

        if (!$product)
        {
            throw new \Exception('Product not found.');
        }

        return $product;
    }

    protected function addNewProductToCart(Carts $cart, array $cartItems)
    {
        $product = $this->getProductInfo($cartItems['product_id']);

        $cart->products()->attach($cartItems['product_id'], [
            'quantity' => $cartItems['quantity'],
            'price' => $product->price ,
        ]);

        return $this->getCart();
    }

    protected function updateProductInCart(Carts $cart, $existingProduct, array $cartItems)
    {
        $product = $this->getProductInfo($cartItems['product_id']);

        if ($cartItems['quantity'] > $product->stock)
        {
            throw new \Exception('Product quantity exceeded.');
        }

        $newQuantity = $existingProduct->pivot->quantity + $cartItems['quantity'];
        $newPrice = $existingProduct->pivot->price + $product->price;

        $cart->products()->updateExistingPivot($cartItems['product_id'], [
            'quantity' => $newQuantity,
            'price' => $existingProduct['price'],
        ]);

        return $this->getCart();
    }

    public function updateCartItem(int $productId, array $cartItems)
    {
        $cart = $this->getCart();

        $existingProduct = $cart->products()->where('product_id', $productId)->first();

        if ($existingProduct) {
            $product = $this->getProductInfo($productId);

            if (!$product) {
                throw new \Exception('Product not found.');
            }

            if ($cartItems['quantity'] > $product->stock)
            {
                throw new \Exception('Product quantity exceeded.');
            }

            $cart->products()->updateExistingPivot($productId, [
                'quantity' => $cartItems['quantity'],
                'price' => $product->price,
            ]);

            return $this->getCart();
        }

        throw new \Exception('Product not found in cart.');
    }



    public function removeCartItem(int $productId)
    {
        $cart = $this->getCart();
        $existingProduct = $cart->products()->where('product_id', $productId)->first();

        if (!$existingProduct)
        {
            throw new \Exception('Product not found.');
        }

        $cart->products()->detach($productId);

        return $this->getCart();
    }




    protected function createCart()
    {
        $cart = new Carts();
        $cart->user_id = Auth::user()->id;
        $cart->save();

        return $cart;
    }
}
