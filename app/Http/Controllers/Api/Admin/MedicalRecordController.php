<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\MedicalRecordRequest;
use App\Http\Resources\Api\Admin\MedicalRecordResource;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Catch_;

class MedicalRecordController extends Controller {
    use ApiResponse;
    public function index()
    {
        try{
            $record = MedicalRecord::with([
                'donation',
                'hospital',
                'screener'
            ])->paginate(config('pagination.perPage'));

            return $this->successResponse(
                'Medical records retrieved successfully',
                $this->buildPaginatedResourceResponse(MedicalRecordResource::class, $record),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),500);
        }
    }
    public function store(MedicalRecordRequest $request)
    {
        try{
             $record = MedicalRecord::create($request->validated());
             return $this->successResponse(
                'Medical record created successfully',
                new MedicalRecordResource($record),
                201
             );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $record = MedicalRecord::find($id);

        if(!$record){
            return $this->errorResponse('Medical record not found',404);
        }

        return $this->successResponse(
            'Medical record retrieved successfully',
            new MedicalRecordResource($record),
            200
        );
    }
    public function update(MedicalRecordRequest $request, $id)
    {
        $record = MedicalRecord::find(($id));
        if(!$record) {
            return $this->errorResponse('Medical record not found',404);
        }
        $record->update($request->validated());
        return $this->successResponse(
            'Medical record updated successfully',
            new MedicalRecordResource($record),
            200
        );
    }

    public function destory($id)
    {
        $record = Medicalrecord::find($id);

        if(!$record){
            return $this->errorResponse('Medical record not found', 404);
        }
        $record->delete();
        return $this->successResponse(
            'Medical record deleted successfully',
            null,
            204
        );
    }
}

    