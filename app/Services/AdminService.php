<?php

namespace App\Services;

use App\Models\Carts;
use App\Models\Orders;
use App\Models\User;

class AdminService
{
    public function get_all_orders()
    {
        $orders = Orders::all();
        return $orders;
    }

    public function get_order_by_id(int $id)
    {
        $order = Orders::find($id);
        if (!$order)
        {
            throw new \Exception("Order not found");
        }

        return $order;
    }

    public function get_all_users()
    {
        $users = User::where('role', 'usr')->get();
        return $users;
    }

    public function get_order_by_user_id(int $user_id)
    {
        $orders = Orders::where('user_id', $user_id)->get();
        if (!$orders)
        {
            throw new \Exception("Orders not found");
        }

        return $orders;
    }

    public function update_order_status(int $id, string $status)
    {
        $order = $this->get_order_by_id($id);
        $order->status = $status;
        $order->save();

        return $order;
    }

    public function delete_user(int $user_id)
    {
        $user = User::find($user_id);

        if (!$user)
        {
            throw new \Exception("User not found");
        }

        $user->delete();

        $orders = $this->get_order_by_user_id($user_id);
        foreach ($orders as $order)
        {
            $order->delete();
        }

        $carts = Carts::where('user_id', $user_id)->get();
        foreach ($carts as $cart)
        {
            $cart->delete();
        }

        return $user;
    }
}
