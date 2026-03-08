<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use App\Models\BloodInventory;
use App\Http\Resources\Api\Admin\BloodInventoryResource;

class BloodInventoryController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        try {
            // Optional filter: by status
            $status = $request->query('status');

            $query = BloodInventory::with(['hospital', 'bloodRequest']);

            if (!is_null($status)) {
                $query->where('status', $status);
            }

            // Paginate
            $perPage = $request->query('per_page', config('pagination.perPage', 10));
            $paginated = $query->paginate($perPage);

            $grouped = $paginated->getCollection()
                ->groupBy('blood_group')
                ->map(function ($items, $group) {
                    return [
                        'blood_group' => $group,
                        'inventories' => BloodInventoryResource::collection($items), 
                    ];
                })->values();

            $paginated->setCollection($grouped);

            return $this->successResponse(
                'Blood inventories retrieved successfully',
                $paginated,
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
