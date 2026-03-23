<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\AppointmentStatus;
use App\Enums\BloodInventoryStatus;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\Appointment;
use App\Models\BloodInventory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        $user = auth()->user();

        $upcomingAppointment = Appointment::with('hospital:id,name')
            ->where('user_id', $user->id)
            ->where('appointment_date', '>=', Carbon::today())
            ->whereIn('status', [
                AppointmentStatus::SCHEDULED->value,
                AppointmentStatus::CONFIRMED->value
            ])
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->first();

        $bloodInventory = BloodInventory::with('hospital:id,name')
            ->where('status', BloodInventoryStatus::AVAILABLE->value)
            ->where('expired_at', '>=', Carbon::today())
            ->select('hospital_id', 'blood_group', DB::raw('SUM(units) as total_units'))
            ->groupBy('hospital_id', 'blood_group')
            ->get()
            ->groupBy('hospital_id');

        return $this->successResponse("Home data fetched successfully", [
            'upcomingAppointment' => $upcomingAppointment ? [
                'id' => $upcomingAppointment->id,
                'hospitalName' => $upcomingAppointment->hospital->name,
                'appointmentDate' => $upcomingAppointment->appointment_date,
                'appointmentTime' => $upcomingAppointment->appointment_time,
                'status' => $upcomingAppointment->status,
                'remarks' => $upcomingAppointment->remarks,
                'type' => $upcomingAppointment->blood_request_id ? 'REQUEST' : 'DONATION',
            ] : null,

            'bloodUnitsByHospital' => $bloodInventory->map(function ($items) {
                return [
                    'hospitalId' => $items->first()->hospital_id,
                    'hospitalName' => $items->first()->hospital->name,
                    'inventory' => $items->map(fn($item) => [
                        'bloodGroup' => $item->blood_group,
                        'totalUnits' => (int) $item->total_units,
                    ]),
                ];
            })->values(),
        ]);
    }
}
