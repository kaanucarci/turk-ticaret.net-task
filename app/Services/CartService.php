<?php
// app/Services/CartService.php

namespace App\Services;

use App\Models\Carts;
use App\Models\Products;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Sepete ürün ekler ya da var olan ürünü günceller.
     *
     * @param array $cartItems
     * @return \App\Models\Carts
     */
    public function addToCart(array $cartItems)
    {
        $cart = $this->getCart();

        $existingProduct = $cart->products()->where('product_id', $cartItems['product_id'])->first();

        if ($existingProduct) {
            return $this->updateProductInCart($cart, $existingProduct, $cartItems);
        }

        return $this->addNewProductToCart($cart, $cartItems);
    }

    /**
     * Kullanıcının mevcut sepetini alır.
     *
     * @return \App\Models\Carts
     */
    public function getCart()
    {
        $cart = Carts::where('user_id', Auth::user()->id)->first();

        if (!$cart)
        {
            $cart = $this->createCart();
        }

        return $cart;
    }

    /**
     * Kullanıcının mevcut sepetini alır.
     *
     * @return \App\Models\Carts
     */
    public function getProductInfo($productId)
    {
        $product = Products::find($productId);

        if (!$product)
        {
            throw new \Exception('Product not found.');
        }

        return $product;
    }

    /**
     * Sepete yeni bir ürün ekler.
     *
     * @param \App\Models\Carts $cart
     * @param array $cartItems
     * @return \App\Models\Carts
     */
    protected function addNewProductToCart(Carts $cart, array $cartItems)
    {
        $product = $this->getProductInfo($cartItems['product_id']);

        $cart->products()->attach($cartItems['product_id'], [
            'quantity' => $cartItems['quantity'],
            'price' => $product->price ,
        ]);

        return $cart;
    }

    /**
     * Var olan ürünü sepette günceller.
     *
     * @param \App\Models\Carts $cart
     * @param \App\Models\Products $existingProduct
     * @param array $cartItems
     * @return \App\Models\Carts
     */
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

        return $cart;
    }

    /**
     * Sepetteki ürünü günceller.
     *
     * @param int $productId
     * @param array $cartItems
     * @return \App\Models\Carts
     */
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

            return $cart;
        }

        throw new \Exception('Product not found in cart.');
    }


    /**
     * Sepetten bir ürünü siler.
     *
     * @param int $productId
     * @return \App\Models\Carts
     */
    public function removeCartItem(int $productId)
    {
        $cart = $this->getCart();
        $existingProduct = $cart->products()->where('product_id', $productId)->first();

        if (!$existingProduct)
        {
            throw new \Exception('Product not found.');
        }

        $cart->products()->detach($productId);

        return $cart;
    }



    /**
     * Eğer kullanıcıda sepet yoksa, yeni bir sepet oluşturur.
     *
     * @return \App\Models\Carts
     */
    protected function createCart()
    {
        $cart = new Carts();
        $cart->user_id = Auth::user()->id;
        $cart->save();

        return $cart;
    }
}
