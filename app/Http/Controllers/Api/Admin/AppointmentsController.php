<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\AppointmentRequest;
use App\Http\Resources\Api\Admin\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    use ApiResponse;
    //appointments index
    public function index()
    {
        $appointments = Appointment::with(['user', 'hospital'])
            ->latest()
            ->paginate(config('pagination.perPage'));

        return $this->successResponse('Appointment Lists', AppointmentResource::collection($appointments));
    }

    //appointments update
    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        try {

            $appointment->update($request->validated());

            return $this->successResponse("Appointment Updated Successfully!", new AppointmentResource($appointment));

        } catch(\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
