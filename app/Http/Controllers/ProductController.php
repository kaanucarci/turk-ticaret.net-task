<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
     * @OA\GET(
     *   path="/products",
     *   tags={"Products"},
     *   summary="Get all products",
     *   @OA\Response(response=200,description="Returns all products",)
     * ),
     */
    public function index()
    {
        $products = Products::all();
        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/products/{product_id}",
     *     tags={"Products"},
     *     summary="Get a specific product by ID",
     *     description="Returns product details based on the provided product ID.",
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Sample Product"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="description", type="string", example="This is a sample product."),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-18T12:34:56Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-18T12:34:56Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */

    public function show($product_id)
    {
         $product = Products::find($product_id);
         if (is_null($product)) {
             return response()->json(['message' => 'Product not found'], 404);
         }
         return response()->json($product);
    }

    /**
     * @OA\POST(
     *   path="/products",
     *   tags={"Products"},
     *   summary="Create a new product",
     *   description="Creates a new product. Only accessible by admin users.",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="name", type="string", example="Sample Product"),
     *           @OA\Property(property="price", type="number", example=99.99),
     *           @OA\Property(property="stock", type="integer", example=100),
     *           @OA\Property(property="description", type="string", example="This is a sample product."),
     *       )
     *   ),
     *   @OA\Response(
     *       response=201,
     *       description="Product created successfully",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Product created successfully")
     *       )
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="Forbidden - Only admins can create products",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Access denied")
     *       )
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Validation Error",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="The given data was invalid."),
     *           @OA\Property(property="errors", type="object")
     *       )
     *   )
     * )
     */
    public function store(Request $request)
    {
        $new_product = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'description' => 'required',
        ]);

        $product = new Products();
        $product->name = $new_product['name'];
        $product->price = $new_product['price'];
        $product->stock = $new_product['stock'];
        $product->description = $new_product['description'];
        $product->save();

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    /**
     * @OA\Put(
     *   path="/products/{product_id}",
     *   tags={"Products"},
     *   summary="Update an existing product",
     *   description="Updates the details of a specific product. Only admins can access this endpoint.",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *       name="product_id",
     *       in="path",
     *       description="ID of the product to update",
     *       required=true,
     *       @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="name", type="string", example="Updated Product Name"),
     *           @OA\Property(property="price", type="number", format="float", example=150.75),
     *           @OA\Property(property="stock", type="integer", example=20),
     *           @OA\Property(property="description", type="string", example="Updated product description")
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Product updated successfully",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Product updated successfully")
     *       )
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Product not found",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Product not found")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthorized",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Unauthorized")
     *       )
     *   )
     * )
     */

    public function update(Request $request, $product_id)
    {
        $new_product = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'description' => 'required',
        ]);

        $product = Products::find($product_id);
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->name = $new_product['name'];
        $product->price = $new_product['price'];
        $product->stock = $new_product['stock'];
        $product->description = $new_product['description'];
        $product->save();

        return response()->json(['message' => 'Product updated successfully'], 200);
    }


    /**
     * @OA\Delete(
     *   path="/products/{product_id}",
     *   tags={"Products"},
     *   summary="Delete a product",
     *   description="Deletes a specific product. Only admins can access this endpoint.",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *       name="product_id",
     *       in="path",
     *       description="ID of the product to delete",
     *       required=true,
     *       @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Product deleted successfully",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Product deleted successfully")
     *       )
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Product not found",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Product not found")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthorized",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Unauthorized")
     *       )
     *   )
     * )
     */

    public function destroy($product_id)
    {
        $product = Products::find($product_id);
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
