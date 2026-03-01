<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\Admin\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    use ApiResponse;
    //appointments index
    public function index()
    {
        $appointments = Appointment::with(['user', 'hospital'])->latest()->get();

        return $this->successResponse('Appointments Index', AppointmentResource::collection($appointments));
    }
}
