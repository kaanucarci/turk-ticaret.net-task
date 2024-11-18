<?php

namespace App\Http\Controllers;

use App\Models\CartItems;
use App\Models\Carts;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @OA\Get(
     *     path="/cart",
     *     summary="Kullanıcının sepetini getirir",
     *     operationId="getCart",
     *     tags={"Cart"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sepet başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="cart", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Cart not found")
     * )
     */
    public function index()
    {
        $cart = $this->cartService->getCart();

        return response()->json($cart);
    }

    /**
     * Sepete ürün ekler.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/cart/items",
     *      summary="Sepete ürün ekler",
     *      operationId="addToCart",
     *      tags={"Cart"},
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"product_id", "quantity"},
     *              @OA\Property(property="product_id", type="integer", example=1),
     *              @OA\Property(property="quantity", type="integer", example=2)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product added to cart successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product added to cart successfully!"),
     *              @OA\Property(property="cart", type="object")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthorized")
     *  )
     */



    public function store(Request $request)
    {
        $cartItems = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer'
        ]);

        try{
            $cart = $this->cartService->addToCart($cartItems);
            return response()->json(['message' => 'Product added to cart successfully!', 'cart' => $cart]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Sepete ürün ekler.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *      path="/cart/items/{product_id}",
     *      summary="Sepetteki bir ürünü günceller",
     *      operationId="updateCartItem",
     *      tags={"Cart"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="product_id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"quantity"},
     *              @OA\Property(property="quantity", type="integer", example=3)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product updated successfully!"),
     *              @OA\Property(property="cart", type="object")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Product not found in cart")
     *  )
     */
    public function update($product_id, Request $request)
    {

        $cartItems = $request->validate([
            'quantity' => 'required|integer',
        ]);

        try {
            $cart = $this->cartService->updateCartItem($product_id, $cartItems);
            return response()->json(['message' => 'Product updated successfully!', 'cart' => $cart]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Sepetten bir ürünü siler.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *      path="/cart/items/{id}",
     *      summary="Sepetten bir ürünü siler",
     *      operationId="removeCartItem",
     *      tags={"Cart"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product removed from cart successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product removed from cart successfully!"),
     *              @OA\Property(property="cart", type="object")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Product not found")
     *  )
     */
    public function destroy($id)
    {
        try {
            $cart = $this->cartService->removeCartItem($id);
            return response()->json(['message' => 'Product removed from cart successfully!', 'cart' => $cart]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
