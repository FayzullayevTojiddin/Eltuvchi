<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegionResource;
use App\Http\Resources\TaxoParkResource;
use App\Models\Region;

class RegionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/regions",
     *     summary="Get list of regions",
     *     tags={"Regions"},
     *     @OA\Response(
     *         response=200,
     *         description="Regions list retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Region")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $regions = Region::where('status', 'active')->get();
        return $this->response(RegionResource::collection($regions), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/regions/{region_id}",
     *     summary="Get region with associated taxoparks",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="region_id",
     *         in="path",
     *         description="ID of the region",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Region and its taxoparks retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="region", ref="#/components/schemas/Region"),
     *                 @OA\Property(
     *                     property="taxoparks",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Taxopark")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Region not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Region not found."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items()
     *             )
     *         )
     *     )
     * )
     */
    public function show($region_id)
    {
        $region = Region::with('taxoparks')->find($region_id);

        if (!$region) {
            return $this->error([], 404, 'Region not found.');
        }

        $data = [
            'region' => new RegionResource($region),
            'taxoparks' => TaxoParkResource::collection($region->taxoparks)
        ];

        return $this->response($data, 200);
    }
}