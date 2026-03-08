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

    public function markUsed(Request $request, $id)
    {
        try {
            // Validate body
            $validated = $request->validate([
                'blood_request_id' => ['required', 'integer', 'exists:blood_requests,id'],
            ]);

            $bloodRequestId = $validated['blood_request_id'];

            // Find inventory
            $inventory = BloodInventory::findOrFail($id);

            // Handle enum casting (if status is cast to enum in model)
            $currentStatus = $inventory->status->value;

            // Check if inventory is available
            if ($currentStatus !== BloodInventoryStatus::AVAILABLE->value) {
                return $this->errorResponse(
                    "This blood has been used.",
                    400
                );
            }

            // Mark as used and assign blood_request_id
            $inventory->status = BloodInventoryStatus::USED->value;
            $inventory->blood_request_id = $bloodRequestId;
            $inventory->save();

            // Refresh so resource shows updated data
            $inventory->refresh();

            return $this->successResponse(
                'Blood inventory marked as used successfully',
                new BloodInventoryResource($inventory), 200
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
