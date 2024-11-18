<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }


    /**
     * @OA\Get(
     *     path="/admin/orders",
     *     summary="Get all orders",
     *     description="Retrieve all orders. Only accessible by admins.",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_all_orders()
    {
        $orders = $this->adminService->get_all_orders();

        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/admin/orders/{id}",
     *     summary="Get order by ID",
     *     description="Retrieve a specific order by ID. Only accessible by admins.",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the order",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function get_order_by_id($id)
    {
        try {
            $order = $this->adminService->get_order_by_id($id);
            return response()->json($order);
        }
        catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/admin/users",
     *     summary="Get all users",
     *     description="Retrieve all users. Only accessible by admins.",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_all_users()
    {
        $users = $this->adminService->get_all_users();

        return response()->json($users);
    }


    /**
     * @OA\Get(
     *     path="/admin/users/{user_id}/orders",
     *     summary="Get orders by user ID",
     *     description="Retrieve all orders made by a specific user. Only accessible by admins.",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of orders for the user",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_order_by_user_id($id)
    {
        try {
            $order = $this->adminService->get_order_by_user_id($id);
            return response()->json($order);
        }
        catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }



    /**
     * @OA\Put(
     *     path="/admin/orders/{id}",
     *     summary="Update order status",
     *     description="Update the status of an order by ID. Only accessible by admins.",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the order to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="The new status of the order",
     *                 enum={"ordered", "completed", "cancelled"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order status updated successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input or bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update_order_status(Request $request, $id)
    {
        $request->validate([
           'status' =>'required|string|in:ordered,completed,cancelled'
        ]);

        try {
            $order = $this->adminService->update_order_status($id, $request->status);
            return response()->json($order);
        }
        catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
            }
    }


    /**
     * @OA\Delete(
     *     path="/admin/users/{id}",
     *     summary="Delete user by ID",
     *     description="Delete a specific user by ID. Only accessible by admins.",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to be deleted",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function delete_user($id)
    {
        try {
            $this->adminService->delete_user($id);
            return response()->json("User has been deleted");
        }
        catch (\Exception $e)
        {
            return response()->json($e->getMessage(), 400);
        }
    }

}
