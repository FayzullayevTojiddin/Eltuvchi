<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminDriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;

class AdminDriverController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/super-admin/drivers",
     *     summary="Get paginated list of drivers",
     *     tags={"Admin Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of drivers",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AdminDriver"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $drivers = Driver::with('user', 'taxopark')->paginate(20);
        return $this->response(data: AdminDriverResource::collection($drivers));
    }

    /**
     * @OA\Get(
     *     path="/api/super-admin/drivers/{driver}",
     *     summary="Get a single driver",
     *     tags={"Admin Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         description="Driver ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver details",
     *         @OA\JsonContent(ref="#/components/schemas/AdminDriver")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Driver not found")
     * )
     */
    public function show(Driver $driver)
    {
        $driver->load(['user', 'taxopark', 'orders']);
        return $this->response(data: new AdminDriverResource($driver));
    }

    /**
     * @OA\Put(
     *     path="/api/super-admin/drivers/{driver}",
     *     summary="Update a driver",
     *     tags={"Admin Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         description="Driver ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="balance", type="integer"),
     *             @OA\Property(property="points", type="integer"),
     *             @OA\Property(property="details", type="object"),
     *             @OA\Property(property="settings", type="object"),
     *             @OA\Property(property="taxopark_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver updated",
     *         @OA\JsonContent(ref="#/components/schemas/AdminDriver")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Driver not found")
     * )
     */
    public function update(Request $request, Driver $driver)
    {
        $driver->fill($request->all());
        $driver->save();
        return $this->success(data: new AdminDriverResource($driver), message: 'Driver updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/super-admin/drivers/{driver}",
     *     summary="Mark driver as inactive",
     *     tags={"Admin Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         description="Driver ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver marked as inactive",
     *         @OA\JsonContent(ref="#/components/schemas/AdminDriver")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Driver not found")
     * )
     */
    public function destroy(Driver $driver)
    {
        $driver->status = 'inactive';
        $driver->save();
        return $this->success(data: new AdminDriverResource($driver), message: 'Driver marked as inactive');
    }

    /**
     * @OA\Post(
     *     path="/api/super-admin/drivers",
     *     summary="Create a new driver",
     *     tags={"Admin Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="balance", type="integer"),
     *             @OA\Property(property="points", type="integer"),
     *             @OA\Property(property="details", type="object"),
     *             @OA\Property(property="settings", type="object"),
     *             @OA\Property(property="taxopark_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Driver created",
     *         @OA\JsonContent(ref="#/components/schemas/AdminDriver")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $driver = Driver::create($data);
        return $this->success(data: new AdminDriverResource($driver), message: 'Driver created successfully');
    }
}