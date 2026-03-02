<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\Admin\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentsController extends Controller
{
    use ApiResponse;
    //appointments index
    public function index()
    {
        $appointments = Appointment::with(['user', 'hospital'])
            ->latest()
            ->paginate(config('pagination.perPage'));

        return $this->successResponse('Appointments Index', AppointmentResource::collection($appointments));
    }

    //appointments update
    public function update(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled, cancelled, confirmed, completed',
            'remarks' => 'nullable|string',
        ]);

        if($validator->fails()) {
            return $this->errorResponse("Validation Error", 422);
        }

        try {

            $appointment->update($validator->validated());

            return $this->successResponse("Appointment Updated Successfully!", new AppointmentResource($appointment));

        } catch(\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
