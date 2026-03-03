<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\Admin\HospitalResource;
use App\Models\Hospital;

class HospitalController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $hospital = Hospital::paginate(config('pagnation.perPage'));

        return $this->successResponse("Hospitals retrieved successfully", $this->buildPaginatedResourceResponse(HospitalResource::class, $hospital), 200);
    }
}
