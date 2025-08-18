<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminDispatcherResource;
use App\Models\Dispatcher;
use Illuminate\Http\Request;

class AdminDispatcherController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/super-admin/dispatchers",
     *     summary="Get list of all dispatchers",
     *     tags={"Admin Dispatcher"},
     *     @OA\Response(
     *         response=200,
     *         description="List of dispatchers",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/AdminDispatcher")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $dispatchers = Dispatcher::all();
        return $this->response(data: AdminDispatcherResource::collection($dispatchers), status: 200);
    }

    /**
     * @OA\Get(
     *     path="/api/super-admin/dispatchers/{dispatcher}",
     *     summary="Get single dispatcher",
     *     tags={"Admin Dispatcher"},
     *     @OA\Parameter(
     *         name="dispatcher",
     *         in="path",
     *         required=true,
     *         description="Dispatcher ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dispatcher details",
     *         @OA\JsonContent(ref="#/components/schemas/AdminDispatcher")
     *     )
     * )
     */
    public function show(Dispatcher $dispatcher)
    {
        $dispatcher->load(['taxopark', 'user']);
        return $this->response(data: new AdminDispatcherResource($dispatcher), status: 200);
    }

    /**
     * @OA\Post(
     *     path="/api/super-admin/dispatchers",
     *     summary="Create new dispatcher",
     *     tags={"Admin Dispatcher"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","taxopark_id","full_name"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="taxopark_id", type="integer"),
     *             @OA\Property(property="full_name", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="details", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Dispatcher created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'taxopark_id' => 'required|exists:taxo_parks,id',
            'full_name' => 'required|string|max:255',
            'status' => 'boolean',
            'details' => 'nullable|array'
        ]);
        if (!isset($data['status'])) {
            $data['status'] = true;
        }
        $dispatcher = Dispatcher::create($data);
        return $this->success(data: new AdminDispatcherResource($dispatcher), status: 201, message: 'Dispatcher created');
    }

    /**
     * @OA\Put(
     *     path="/api/super-admin/dispatchers/{dispatcher}",
     *     summary="Update dispatcher",
     *     tags={"Admin Dispatcher"},
     *     @OA\Parameter(
     *         name="dispatcher",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="full_name", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="details", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Dispatcher updated")
     * )
     */
    public function update(Request $request, Dispatcher $dispatcher)
    {
        $data = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'status' => 'sometimes|boolean',
            'details' => 'nullable|array'
        ]);
        $dispatcher->update($data);
        return $this->success(data: new AdminDispatcherResource($dispatcher), status: 200, message: 'Dispatcher updated');
    }

    /**
     * @OA\Delete(
     *     path="/api/super-admin/dispatchers/{dispatcher}",
     *     summary="Deactivate dispatcher (soft delete)",
     *     tags={"Admin Dispatcher"},
     *     @OA\Parameter(
     *         name="dispatcher",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Dispatcher deactivated")
     * )
     */
    public function destroy(Dispatcher $dispatcher)
    {
        $dispatcher->update(['status' => false]);
        return $this->success(data: new AdminDispatcherResource($dispatcher), status: 200, message: 'Dispatcher deactivated');
    }
}