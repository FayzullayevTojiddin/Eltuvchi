<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\DriverProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverMarketController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/driver/market",
     *     summary="List available products",
     *     description="Retrieve all active products that can be purchased by drivers.",
     *     tags={"Driver Market"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             ),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success")
     *         )
     *     )
     * )
     */
    public function list_products()
    {
        $products = Product::active()->get();
        return $this->response(ProductResource::collection($products));
    }

    /**
     * @OA\Post(
     *     path="/api/driver/market/products/{product}",
     *     summary="Purchase a product",
     *     description="Driver purchases a product using points. Points will be deducted if sufficient balance exists.",
     *     tags={"Driver Market"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product purchased successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product successfully purchased."),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Insufficient points or product unavailable.")
     *         )
     *     )
     * )
     */
    public function get_product(Product $product)
    {
        $driver = Auth::user()->driver;

        if (! $product->status) {
            return $this->error(error_message: 'Product is not available.', status: 422);
        }

        return DB::transaction(function () use ($driver, $product) {
            if (! $driver->subtractPoints($product->points, "Purchased product #{$product->id}")) {
                return $this->error(error_message: 'Insufficient points.', status: 422);
            }

            $driverProduct = DriverProduct::create([
                'driver_id'  => $driver->id,
                'product_id' => $product->id,
                'delivered'  => false,
            ]);

            return $this->success(
                data: new ProductResource($product), 
                status: 200, 
                message: 'Product successfully purchased.'
            );
        });
    }
}