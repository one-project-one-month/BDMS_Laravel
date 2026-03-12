<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\User\CertificateResource;
use App\Models\Certificate;

class CertificateController extends Controller
{
    use ApiResponse;
    public function index($userId)
    {
        try {
            $certificates = Certificate::with(['users'])->where('user_id', $userId)->paginate(config('pagnation.perPage'));

            return $this->successResponse('Certificates retrieved successfully', $this->buildPaginatedResourceResponse(CertificateResource::class, $certificates), 200);
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($userId, $id)
    {
        try {
            $certificate = Certificate::with(['users'])
                ->where([
                    'user_id' => $userId,
                    'id' => $id
                ])
                ->firstOrFail();

            return $this->successResponse('Certificate retrieved successfully', new CertificateResource($certificate), 200);
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }
}
