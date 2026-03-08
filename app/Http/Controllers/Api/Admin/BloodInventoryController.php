<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use App\Models\BloodInventory;
use App\Http\Resources\Api\Admin\BloodInventoryResource;
use App\Enums\BloodInventoryStatus;

class BloodInventoryController extends Controller
{
    use ApiResponse;
    /**
     * GET /blood-inventories
     * List blood inventories, grouped by blood group, with pagination
     */
    public function index(Request $request)
    {
        try {
            $status = $request->query('status');
            $perPage = $request->query('per_page', config('pagination.perPage', 10));

            // Base query with optional status filter
            $query = BloodInventory::with(['hospital', 'bloodRequest']);
            if (!is_null($status)) {
                $query->where('status', $status);
            }

            // Paginate first
            $paginated = $query->paginate($perPage);

            // Group current page by blood_group
            $grouped = $paginated->getCollection()
                ->groupBy('blood_group')
                ->map(function ($items, $group) {
                    return [
                        'blood_group' => $group,
                        'inventories' => BloodInventoryResource::collection($items),
                    ];
                })->values();

            // Replace paginated collection with grouped collection
            $paginated->setCollection($grouped);

            return $this->successResponse(
                'Blood inventories retrieved successfully',
                $paginated, // grouped paginated collection
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * GET /blood-inventories/{id}
     * Show single inventory detail
     */
    public function show($id)
    {
        try {
            $inventory = BloodInventory::with(['hospital', 'bloodRequest'])->findOrFail($id);
            return $this->successResponse(
                'Blood inventory retrieved successfully',
                new BloodInventoryResource($inventory),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function markUsed($id)
    {
        $inventory = BloodInventory::findOrFail($id);
        $status = BloodInventoryStatus::AVAILABLE->value;
        try {
            if ($inventory->status->value !== $status) {
                return $this->errorResponse(
                "Inventory is not available to mark as used. Current status: {$status}",
                400
            );
            }

            $inventory->update([
                'status' => BloodInventoryStatus::USED->value,
            ]);

            return $this->successResponse(
                'Blood inventory marked as used successfully',
                new BloodInventoryResource($inventory)
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
