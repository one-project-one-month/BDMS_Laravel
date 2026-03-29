<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Donor;
use DB;

/**
 * @OA\Schema(
 * schema="HomeDataResponse",
 * title="Home Data Response",
 * @OA\Property(
 * property="upcomingAppointment",
 * type="object",
 * nullable=true,
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="hospitalName", type="string", example="Yangon General Hospital"),
 * @OA\Property(property="appointmentDate", type="string", format="date", example="2026-03-25"),
 * @OA\Property(property="appointmentTime", type="string", example="10:00 AM"),
 * @OA\Property(property="status", type="string", example="confirmed"),
 * @OA\Property(property="remarks", type="string", example="Please bring your donor card."),
 * @OA\Property(property="type", type="string", enum={"REQUEST", "DONATION"}, example="DONATION")
 * ),
 * @OA\Property(
 * property="bloodUnitsByHospital",
 * type="array",
 * @OA\Items(
 * @OA\Property(property="hospitalId", type="integer", example=5),
 * @OA\Property(property="hospitalName", type="string", example="City Care Clinic"),
 * @OA\Property(
 * property="inventory",
 * type="array",
 * @OA\Items(
 * @OA\Property(property="bloodGroup", type="string", example="O+"),
 * @OA\Property(property="totalUnits", type="integer", example=12)
 * )
 * )
 * )
 * )
 * )
 */
class HomeController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/user/home",
     * summary="Get dashboard data for the user home screen",
     * description="Returns the user's next upcoming appointment and a list of available blood units grouped by hospital.",
     * tags={"Home For Client"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Home data retrieved successfully",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Home data fetched successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/HomeDataResponse")
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index()
    {
        try {
            //Lives Saved
            $livesSaved = BloodRequest::where('status', 'fulfilled')->count();

            //Registered Donors
            $registeredDonors = Donor::count();

            //Active Requests
            $activeRequests = BloodRequest::where('status', 'pending')->count();

            //Blood Donations
            $bloodDonations = Donation::count();

            //BloodAvailability from Inventory
            $bloodAvailability = BloodInventory::select(
                'blood_group',
                DB::raw('SUM(units) as total_units')
            )
                ->where('status', 'available')
                ->where('expired_at', '>', now())
                ->groupBy('blood_group')
                ->get()
                ->map(function ($inventory) {
                    return [
                        'bloodGroup' => $inventory->blood_group,
                        'units' => (int) $inventory->total_units,
                    ];
                });

            return $this->successResponse("Home data fetched successfully", [
                'livesSaved' => $livesSaved,
                'registeredDonors' => $registeredDonors,
                'activeRequests' => $activeRequests,
                'bloodDonations' => $bloodDonations,
                'bloodAvailability' => $bloodAvailability,
            ]);


        } catch (\Exception $e) {
            return $this->errorResponse("Failed to fetch index" . $e->getMessage(), 500);
        }
    }
}
