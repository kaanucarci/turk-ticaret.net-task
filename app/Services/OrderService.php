<?php

namespace App\Services;
use App\Models\CartItems;
use App\Models\Carts;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Support\Facades\Auth;


class OrderService
{
    public function create_order(array $request)
    {
        $cart = Carts::where('user_id', Auth::user()->id)
            ->where('id', $request['cart_id'])
            ->where('status', 'active')
            ->first();

        if (!$cart)
        {
            throw new \Exception("Cart not found!");
        }


        $cart_items = CartItems::where('cart_id', $cart->id)->get();

        foreach ($cart_items as $cart_item)
        {
            $product = Products::find($cart_item->product_id);
            if ($product->stock < $cart_item->quantity)
            {
                throw new \Exception("Product stock is not enough!");
            }
        }


        $order = new Orders();
        $order->user_id = Auth::user()->id;
        $order->cart_id = $cart->id;
        $order->total_amount = $cart_items->sum('price');
        $order->save();

        $this->update_product_stock($cart_items);
        $this->update_cart_status($cart->id);

        $order->order_items = $cart_items;
        return $order;
    }

    public function get_all_orders()
    {
         $orders = Orders::where('user_id', Auth::user()->id)->get();

         foreach ($orders as &$order)
         {
             $order_items = CartItems::where('cart_id', $order->cart_id)->first();
             $order->order_items = $order_items;
         }

         return $orders;

    }

    public function get_order_by_id($id)
    {
        $order = Orders::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->first();

        $cart_items = CartItems::where('cart_id', $order->cart_id)->get();

        $order->order_items = $cart_items;

        if (!$order)
        {
            throw new \Exception("Order not found!");
        }

        return $order;
    }


    protected function update_product_stock($cart_items)
    {
        foreach ($cart_items as $cart_item)
        {
            $product = Products::find($cart_item->product_id);
            $product->stock -= $cart_item->quantity;
            $product->save();
        }
    }

    protected function update_cart_status($id)
    {
         $cart = Carts::find($id);
         $cart->status = 'completed';
         $cart->save();
    }
}
