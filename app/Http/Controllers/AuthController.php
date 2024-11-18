<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/auth/register",
     *   tags={"Authentication"},
     *   summary="Register a new user",
     *   description="Registers a new user in the system.",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="name", type="string", example="John Doe"),
     *           @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *           @OA\Property(property="password", type="string", format="password", example="password123"),
     *           @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *       )
     *   ),
     *   @OA\Response(
     *       response=201,
     *       description="User registered successfully",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=1),
     *           @OA\Property(property="name", type="string", example="John Doe"),
     *           @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *           @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-18T12:00:00Z"),
     *           @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-18T12:00:00Z")
     *       )
     *   ),
     *   @OA\Response(
     *       response=400,
     *       description="Validation errors",
     *       @OA\JsonContent(
     *           type="object",
     *           example={
     *               "email": {"The email field is required."}
     *           }
     *       )
     *   )
     * )
     */


    public function register() {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->password = bcrypt(request()->password);
        $user->save();

        return response()->json($user, 201);
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/auth/login",
     *   tags={"Authentication"},
     *   summary="Login with credentials",
     *   description="Login a user by providing email and password and receive a JWT token.",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *           @OA\Property(property="password", type="string", format="password", example="password123")
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Login successful and JWT token returned",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="access_token", type="string", example="your-jwt-token"),
     *           @OA\Property(property="token_type", type="string", example="bearer"),
     *           @OA\Property(property="expires_in", type="integer", example=3600),
     *           @OA\Property(property="message", type="string", example="Login Successful")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthorized - Invalid credentials",
     *       @OA\JsonContent(
     *           type="object",
     *           example={"error": "Unauthorized"}
     *       )
     *   )
     * )
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *   path="/auth/me",
     *   tags={"Authentication"},
     *   summary="Get the authenticated user",
     *   description="Fetch the currently authenticated user's data.",
     *   @OA\Response(
     *       response=200,
     *       description="Authenticated user data",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=1),
     *           @OA\Property(property="name", type="string", example="John Doe"),
     *           @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *           @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-18T12:00:00Z"),
     *           @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-18T12:00:00Z")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthorized",
     *       @OA\JsonContent(
     *           type="object",
     *           example={"error": "Unauthorized"}
     *       )
     *   )
     * )
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/auth/logout",
     *   tags={"Authentication"},
     *   summary="Log out the authenticated user",
     *   description="Log out the authenticated user and invalidate their JWT token.",
     *   @OA\Response(
     *       response=200,
     *       description="User successfully logged out",
     *       @OA\JsonContent(
     *           type="object",
     *           example={"message": "Successfully logged out"}
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthorized - No valid token",
     *       @OA\JsonContent(
     *           type="object",
     *           example={"error": "Unauthorized"}
     *       )
     *   )
     * )
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *   path="/auth/refresh",
     *   tags={"Authentication"},
     *   summary="Refresh JWT token",
     *   description="Refresh the JWT token to keep the user logged in.",
     *   @OA\Response(
     *       response=200,
     *       description="Token successfully refreshed",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="access_token", type="string", example="your-new-jwt-token"),
     *           @OA\Property(property="token_type", type="string", example="bearer"),
     *           @OA\Property(property="expires_in", type="integer", example=3600),
     *           @OA\Property(property="message", type="string", example="Token refreshed successfully")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthorized - Invalid token",
     *       @OA\JsonContent(
     *           type="object",
     *           example={"error": "Unauthorized"}
     *       )
     *   )
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'message' => 'Login Successful',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
