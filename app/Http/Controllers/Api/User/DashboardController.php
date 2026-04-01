<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\AppointmentStatus;
use App\Enums\BloodRequestStatus;
use App\Enums\Urgency;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\BloodRequest;
use App\Models\Donation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('donor');
        $donor = $user->donor;

        $upcomingAppointment = Appointment::with('hospital')
            ->where('user_id', $user->id)
            ->where('appointment_date', '>=', now())
            ->whereIn('status', [
                AppointmentStatus::SCHEDULED->value,
                AppointmentStatus::CONFIRMED->value
            ])
            ->orderBy('appointment_date', 'asc')
            ->first();

        // Urgent Blood Request
        $urgentBloodRequest = BloodRequest::with('hospital')
            ->where('urgency', Urgency::EMERGENCY->value)
            ->where('status', BloodRequestStatus::PENDING->value)
            ->latest()
            ->first();

        // Statistics & Donation Logic
        $totalDonations = $donor
            ? Donation::where('donor_id', $donor->id)->where('status', 'completed')->count()
            : 0;

        $totalRequests = BloodRequest::where('user_id', $user->id)->count();

        // Donation Status Logic
        $nextDonationDate = null;
        $isEligible = true;
        $daysRemaining = 0;

        if ($donor && $donor->last_donation_date) {
            $lastDonation = Carbon::parse($donor->last_donation_date);
            $nextDate = $lastDonation->copy()->addDays(90);
            $nextDonationDate = $nextDate->format('Y-m-d');

            if (now()->lt($nextDate)) {
                $isEligible = false;
                $daysRemaining = (int) now()->diffInDays($nextDate);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved.',
            'data' => [
                'statistics' => [
                    'total_donations' => $totalDonations,
                    'total_blood_requests' => $totalRequests,
                ],
                'upcoming_appointment' => $upcomingAppointment,
                'urgent_blood_request' => $urgentBloodRequest,
                'donation_status' => [
                    'next_eligible_date' => $nextDonationDate,
                    'is_eligible' => $isEligible,
                    'days_remaining' => $daysRemaining,
                    'last_donation_date' => $donor?->last_donation_date ? Carbon::parse($donor->last_donation_date)->format('Y-m-d') : null
                ]
            ]
        ], 200);
    }
}
