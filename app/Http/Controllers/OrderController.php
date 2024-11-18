<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="Kullanıcının siparişlerini getirir",
     *     operationId="getOrders",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Siparişler başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="order", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Orders not found")
     * )
     */

    public function index()
    {
        try {
            $orders = $this->orderService->get_all_orders();
            return response()->json($orders);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     summary="Get an order by ID",
     *     description="Belirtilen ID'ye sahip siparişi getirir. Yalnızca 'usr' rolü ile erişilebilir.",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Sipariş ID'si",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş başarıyla getirildi.",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Hata mesajı"
     *     )
     * )
     */

    public function show($id)
    {
        try {
            $order = $this->orderService->get_order_by_id($id);
            return response()->json($order);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="Create a new order",
     *     description="Yeni bir sipariş oluşturur. Kullanıcı giriş yapmış olmalı ve 'usr' rolüne sahip olmalıdır.",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cart_id"},
     *             @OA\Property(property="cart_id", type="integer", example=1, description="Cart ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sipariş başarıyla oluşturuldu.",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Hata mesajı"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $order = $request->validate([
            'cart_id' => 'required|integer'
        ]);

        try {
            $order = $this->orderService->create_order($order);
            return response()->json([
               'message' => 'Order created successfully',
                'order' => $order
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

}
