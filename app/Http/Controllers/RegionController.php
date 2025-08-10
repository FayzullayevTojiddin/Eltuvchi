<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegionResource;
use App\Http\Resources\TaxoParkResource;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::all();

        return $this->success(RegionResource::collection($regions), 200, 'Regions list retrieved successfully.');
    }

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

        return $this->success($data, 200, 'Associated taxoparks retrieved successfully.');
    }
}