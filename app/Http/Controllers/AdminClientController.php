<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

class AdminClientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/super-admin/clients",
     *     summary="Get paginated list of clients",
     *     tags={"Admin Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of clients",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AdminClient"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $clients = Client::with('user')->paginate(20);
        return $this->response(data: AdminClientResource::collection($clients));
    }

    /**
     * @OA\Get(
     *     path="/api/super-admin/clients/{client}",
     *     summary="Get a single client",
     *     tags={"Admin Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client details",
     *         @OA\JsonContent(ref="#/components/schemas/AdminClient")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Client not found")
     * )
     */
    public function show(Client $client)
    {
        $client->load(['user', 'orders', 'discounts']);
        return $this->response(data: new AdminClientResource($client));
    }

    /**
     * @OA\Put(
     *     path="/api/super-admin/clients/{client}",
     *     summary="Update a client",
     *     tags={"Admin Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="balance", type="integer"),
     *             @OA\Property(property="points", type="integer"),
     *             @OA\Property(property="settings", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client updated",
     *         @OA\JsonContent(ref="#/components/schemas/AdminClient")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Client not found")
     * )
     */
    public function update(Request $request, Client $client)
    {
        $client->fill($request->all());
        $client->save();

        return $this->success(data: new AdminClientResource($client), message: 'Client updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/super-admin/clients/{client}",
     *     summary="Mark client as inactive",
     *     tags={"Admin Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client marked as inactive",
     *         @OA\JsonContent(ref="#/components/schemas/AdminClient")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Client not found")
     * )
     */
    public function destroy(Client $client)
    {
        $client->status = 'inactive';
        $client->save();

        return $this->success(data: new AdminClientResource($client), message: 'Client marked as inactive');
    }
}