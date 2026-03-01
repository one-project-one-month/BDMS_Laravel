<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\DonorRequest;
use App\Http\Resources\Api\Admin\DonorResource;
use App\Models\Donor;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 * name="Donors",
 * description="API Endpoints for managing donors"
 * )
 */

class DonorController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/donors",
     * summary="Get list of all blood donors",
     * description="Returns a list of donors with optional filtering by blood group.",
     * tags={"Donors"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="blood_group",
     * in="query",
     * description="Filter donors by blood group (e.g., A+, B-, O+)",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/DonorResource")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden - Admin access required"
     * )
     * )
     */
    public function index(Request $request)
    {
        $bloodGroup = $request->query('blood_group');
        $query = Donor::with('user');

        if ($bloodGroup) {
            $query->where('blood_group', $bloodGroup);
        }

        $donors = $query->paginate(config('pagination.perPage'));

        return $this->successResponse(
            'Donors retrieved successfully',
            $this->buildPaginatedResourceResponse(DonorResource::class, $donors),
            200
        );
    }

    /**
     * @OA\Post(
     * path="/api/v1/donors",
     * summary="Register a new donor",
     * tags={"Donors"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"userId", "blood_group", "gender", "age"},
     * @OA\Property(property="userId", type="integer", example=5),
     * @OA\Property(property="blood_group", type="string", example="O+"),
     * @OA\Property(property="gender", type="string", example="male"),
     * @OA\Property(property="age", type="integer", example=26),
     * @OA\Property(property="lastDonation", type="string", format="date", example="2025-12-01"),
     * @OA\Property(property="isActive", type="boolean", example=true)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Donor created successfully",
     * @OA\JsonContent(ref="#/components/schemas/DonorResource")
     * )
     * )
     */
    public function store(DonorRequest $request)
    {
        try {
            $donor = Donor::create($request->validated());

            return $this->successResponse(
                'Donor profile created successfully',
                new DonorResource($donor->load('user')),
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/donors/{id}",
     * summary="Display a specific donor",
     * tags={"Donors"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of donor to return",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/DonorResource")
     * ),
     * @OA\Response(response=404, description="Donor not found")
     * )
     */
    public function show($id)
    {
        try {
            $donor = Donor::with('user', 'donations')->findOrFail($id);
            return $this->successResponse('Donor details retrieved', new DonorResource($donor));
        } catch (\Exception $e) {
            return $this->errorResponse('Donor not found', 404);
        }
    }

    /**
     * @OA\Put(
     * path="/api/v1/donors/{id}",
     * summary="Update an existing donor",
     * tags={"Donors"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="blood_group", type="string", example="A+"),
     * @OA\Property(property="age", type="integer", example=27),
     * @OA\Property(property="lastDonation", type="string", format="date", example="2026-02-15")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Donor updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/DonorResource")
     * )
     * )
     */
    public function update(DonorRequest $request, $id)
    {
        try {
            $donor = Donor::findOrFail($id);
            $donor->update($request->validated());

            return $this->successResponse('Donor profile updated', new DonorResource($donor));
        } catch (\Exception $e) {
            return $this->errorResponse('Update failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/v1/donors/{id}",
     * summary="Delete a donor record",
     * tags={"Donors"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="Donor deleted successfully",
     * @OA\JsonContent(@OA\Property(property="message", type="string", example="Donor removed."))
     * )
     * )
     */
    public function destroy($id)
    {
        try {
            $donor = Donor::findOrFail($id);
            $donor->delete();

            return $this->successResponse('Donor record deleted permanently', null);
        } catch (\Exception $e) {
            return $this->errorResponse('Delete failed', 500);
        }
    }

    public function restore($id)
    {
        try {
            $donor = Donor::onlyTrashed()->findOrFail($id);
            $donor->restore();

            return $this->successResponse('Donor restored', new DonorResource($donor));
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $donor = Donor::withTrashed()->findOrFail($id);
            $donor->forceDelete();

            return $this->successResponse('Donor permanently deleted', null, 204);
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/v1/donors/{id}/deactivate",
     * summary="Deactivate a donor",
     * description="Disables the donor from being listed as available for donation.",
     * tags={"Donors"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Donor deactivated")
     * )
     */
    public function deactivate($id)
    {
        try {
            $donor = Donor::findOrFail($id);
            $donor->update(['is_active' => false]);

            return $this->successResponse('Donor account deactivated', new DonorResource($donor));
        } catch (\Exception $e) {
            return $this->errorResponse('Deactivation failed', 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/v1/donors/{id}/activate",
     * summary="Activate a donor",
     * description="Re-enables the donor for the active donation pool.",
     * tags={"Donors"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Donor activated")
     * )
     */
    public function activate($id)
    {
        try {
            $donor = Donor::findOrFail($id);

            if ($donor->is_active) {
                return $this->errorResponse('This donor profile is already active.', 400);
            }

            $donor->update(['is_active' => true]);

            return $this->successResponse(
                'Donor profile has been activated successfully',
                new DonorResource($donor->load('user'))
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
