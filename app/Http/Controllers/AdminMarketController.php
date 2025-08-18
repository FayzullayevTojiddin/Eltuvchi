<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\DiscountResource;
use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;

class AdminMarketController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/super-admin/market",
     *     summary="Get list of products or discounts",
     *     tags={"Admin Market"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Type of items to fetch (product|discount)",
     *         @OA\Schema(type="string", enum={"product", "discount"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of items",
     *     )
     * )
     */
    public function index(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'product') {
            $items = Product::all();
            return $this->response(ProductResource::collection($items));
        }

        if ($type === 'discount') {
            $items = Discount::all();
            return $this->response(DiscountResource::collection($items));
        }

        return $this->error(error_message: 'Invalid type, must be product or discount', status: 400);
    }

    /**
     * @OA\Post(
     *     path="/api/super-admin/market",
     *     summary="Create product or discount",
     *     tags={"Admin Market"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type"},
     *             @OA\Property(property="type", type="string", enum={"product", "discount"}),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="icon_type", type="string"),
     *             @OA\Property(property="points", type="integer"),
     *             @OA\Property(property="value", type="integer"),
     *             @OA\Property(property="percent", type="integer"),
     *             @OA\Property(property="icon", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created successfully")
     * )
     */
    public function store(Request $request)
    {
        $type = $request->get('type');

        if ($type === 'product') {
            $product = Product::create($request->only([
                'status', 'icon_type', 'points', 'title', 'description'
            ]));
            return $this->success(data: new ProductResource($product), status: 201);
        }

        if ($type === 'discount') {
            $discount = Discount::create($request->only([
                'type', 'value', 'points', 'title', 'icon', 'percent', 'active'
            ]));
            return $this->success(data: new DiscountResource($discount), status: 201);
        }

        return $this->error('Invalid type, must be product or discount');
    }

    /**
     * @OA\Put(
     *     path="/api/super-admin/market/{id}",
     *     summary="Update product or discount (only title, description, status)",
     *     tags={"Admin Market"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Type of item (product|discount)",
     *         @OA\Schema(type="string", enum={"product", "discount"})
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $type = $request->query('type');

        if ($type === 'product') {
            $product = Product::findOrFail($id);
            $product->update($request->only(['title', 'description', 'status']));
            return $this->success(new ProductResource($product));
        }

        if ($type === 'discount') {
            $discount = Discount::findOrFail($id);
            $discount->update($request->only(['title', 'status']));
            return $this->success(new DiscountResource($discount));
        }

        return $this->error(error_message: 'Invalid type, must be product or discount', status: 400);
    }
}