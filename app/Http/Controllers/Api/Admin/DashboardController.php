<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\Admin\BloodRequestResource;
use App\Http\Resources\Api\Admin\DonationResource;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donation;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __invoke()
    {
        $dashboardData = [
            'totalDonations'      => Donation::count(),
            'totalBloodRequests'  => BloodRequest::count(),
            'bloodUnitsAvailable' => BloodInventory::availableUnitsByHospital()->with('hospital')->get(),
            'recentDonations'     => DonationResource::collection(Donation::latest()->take(5)->get()),
            'recentBloodRequests' => BloodRequestResource::collection(BloodRequest::latest()->take(5)->get()),
        ];

        return $this->successResponse("Dashboard data retrieved successfully", $dashboardData, 200);
    }
}
