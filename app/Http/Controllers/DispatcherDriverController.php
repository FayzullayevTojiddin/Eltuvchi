<?php

namespace App\Http\Controllers;

use App\Http\Resources\DispatcherDriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatcherDriverController extends Controller
{
    /**
     * @OA\Get(
     *     path="api/dispatcher/drivers",
     *     summary="Get list of drivers in your taxopark",
     *     tags={"Dispatcher Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by driver status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active","inactive","archived"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of drivers",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/DispatcherDriver"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $dispatcher = Auth::user()->dispatcher;
        $status = $request->query('status', 'active');
        $drivers = Driver::where('taxopark_id', $dispatcher->taxopark_id)
                         ->where('status', $status)
                         ->get();
        return $this->response(
            data: DispatcherDriverResource::collection($drivers),
            status: 200
        );
    }

     /**
     * @OA\Get(
     *     path="api/dispatcher/drivers/{driver}",
     *     summary="Get single driver details",
     *     tags={"Dispatcher Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver details",
     *         @OA\JsonContent(ref="#/components/schemas/DispatcherDriver")
     *     ),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function show(Driver $driver)
    {
        $dispatcher = Auth::user()->dispatcher;
        if ($driver->taxopark_id !== $dispatcher->taxopark_id) {
            return $this->error(error_message: 'Unauthorized', status: 403);
        }
        $driver->load(['user', 'taxopark', 'orders']);
        return $this->response(
            data: new DispatcherDriverResource($driver),
            status: 200
        );
    }


    /**
     * @OA\Post(
     *     path="api/dispatcher/drivers",
     *     summary="Create a new driver",
     *     tags={"Dispatcher Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="status", type="string", enum={"active","inactive","archived"}),
     *             @OA\Property(property="balance", type="number", format="float"),
     *             @OA\Property(property="points", type="number", format="float"),
     *             @OA\Property(property="details", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="settings", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Driver created",
     *         @OA\JsonContent(ref="#/components/schemas/DispatcherDriver")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $dispatcher = Auth::user()->dispatcher;

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'string|in:active,inactive,archived',
            'balance' => 'integer|min:0',
            'points' => 'integer|min:0',
            'details' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);
        $data['taxopark_id'] = $dispatcher->taxopark_id;
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        $driver = Driver::create($data);
        return $this->success(
            data: new DispatcherDriverResource($driver),
            status: 201,
            message: 'Driver created'
        );
    }

     /**
     * @OA\Put(
     *     path="api/dispatcher/drivers/{driver}",
     *     summary="Update driver",
     *     tags={"Dispatcher Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"active","inactive","archived"}),
     *             @OA\Property(property="balance", type="number", format="float"),
     *             @OA\Property(property="points", type="number", format="float"),
     *             @OA\Property(property="details", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="settings", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver updated",
     *         @OA\JsonContent(ref="#/components/schemas/DispatcherDriver")
     *     ),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function update(Request $request, Driver $driver)
    {
        $dispatcher = Auth::user()->dispatcher;
        if ($driver->taxopark_id !== $dispatcher->taxopark_id) {
            return $this->error(error_message: 'Unauthorized', status: 403);
        }
        $data = $request->validate([
            'status' => 'string|in:active,inactive,archived',
            'balance' => 'integer|min:0',
            'points' => 'integer|min:0',
            'details' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);
        $driver->update($data);
        return $this->success(
            data: new DispatcherDriverResource($driver),
            status: 200,
            message: 'Driver updated'
        );
    }

     /**
     * @OA\Delete(
     *     path="api/dispatcher/drivers/{driver}",
     *     summary="Deactivate driver",
     *     tags={"Dispatcher Drivers"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver deactivated",
     *         @OA\JsonContent(ref="#/components/schemas/DispatcherDriver")
     *     ),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function destroy(Driver $driver)
    {
        $dispatcher = Auth::user()->dispatcher;
        if ($driver->taxopark_id !== $dispatcher->taxopark_id) {
            return $this->error(error_message: 'Unauthorized', status: 403);
        }
        $driver->update(['status' => 'inactive']);
        return $this->success(
            data: new DispatcherDriverResource($driver),
            status: 200,
            message: 'Driver deactivated'
        );
    }
}