<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminTaxoParkResource;
use App\Models\TaxoPark;
use Illuminate\Http\Request;

class AdminTaxoParkController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/super-admin/taxoparks",
     *     summary="Get paginated list of taxoparks",
     *     tags={"Admin TaxoParks"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of taxoparks",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AdminTaxoParkResource"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $taxoparks = TaxoPark::with('region')->paginate(20);
        return $this->response(data: AdminTaxoParkResource::collection($taxoparks));
    }

    /**
     * @OA\Get(
     *     path="/api/super-admin/taxoparks/{taxopark}",
     *     summary="Get a single taxopark",
     *     tags={"Admin TaxoParks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="taxopark",
     *         in="path",
     *         required=true,
     *         description="TaxoPark ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TaxoPark details",
     *         @OA\JsonContent(ref="#/components/schemas/AdminTaxoParkResource")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="TaxoPark not found")
     * )
     */
    public function show(TaxoPark $taxopark)
    {
        $taxopark->load(['region', 'drivers', 'dispatchers']);
        return $this->response(data: new AdminTaxoParkResource($taxopark));
    }

    /**
     * @OA\Put(
     *     path="/api/super-admin/taxoparks/{taxopark}",
     *     summary="Update a taxopark",
     *     tags={"Admin TaxoParks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="taxopark",
     *         in="path",
     *         required=true,
     *         description="TaxoPark ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="region_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TaxoPark updated",
     *         @OA\JsonContent(ref="#/components/schemas/AdminTaxoParkResource")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="TaxoPark not found")
     * )
     */
    public function update(Request $request, TaxoPark $taxopark)
    {
        $taxopark->fill($request->all());
        $taxopark->save();
        return $this->success(data: new AdminTaxoParkResource($taxopark), message: 'TaxoPark updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/super-admin/taxoparks/{taxopark}",
     *     summary="Mark taxopark as inactive",
     *     tags={"Admin TaxoParks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="taxopark",
     *         in="path",
     *         required=true,
     *         description="TaxoPark ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TaxoPark marked as inactive",
     *         @OA\JsonContent(ref="#/components/schemas/AdminTaxoParkResource")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="TaxoPark not found")
     * )
     */
    public function destroy(TaxoPark $taxopark)
    {
        $taxopark->status = 'inactive';
        $taxopark->save();
        return $this->success(data: new AdminTaxoParkResource($taxopark), message: 'TaxoPark marked as inactive');
    }
}