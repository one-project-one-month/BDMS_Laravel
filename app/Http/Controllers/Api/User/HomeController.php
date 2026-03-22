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

class HomeController extends Controller
{
    use ApiResponse;
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
