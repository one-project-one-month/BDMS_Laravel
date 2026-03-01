<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\DonorRequest;
use App\Http\Resources\Api\Admin\DonorResource;
use App\Models\Donor;
use Illuminate\Http\Request;

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

    public function show($id)
    {
        try {
            $donor = Donor::with('user', 'donations')->findOrFail($id);
            return $this->successResponse('Donor details retrieved', new DonorResource($donor));
        } catch (\Exception $e) {
            return $this->errorResponse('Donor not found', 404);
        }
    }

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
