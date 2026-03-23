<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\User\CertificateResource;
use App\Models\Certificate;

class CertificateController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/{userId}/certificates",
     * summary="Get all certificates for a specific user",
     * tags={"Certificate For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="userId",
     * in="path",
     * description="ID of the user to fetch certificates for",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful retrieval",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Certificates retrieved successfully"),
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/UserCertificateResource")
     * )
     * )
     * ),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index($userId)
    {
        try {
            $certificates = Certificate::with(['users'])->where('user_id', $userId)->paginate(config('pagnation.perPage'));

            return $this->successResponse('Certificates retrieved successfully', $this->buildPaginatedResourceResponse(CertificateResource::class, $certificates), 200);
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/{userId}/certificates/{id}",
     * summary="Get details of a specific certificate",
     * tags={"Certificate For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="userId",
     * in="path",
     * description="User ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Certificate ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful retrieval",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Certificate retrieved successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/UserCertificateResource")
     * )
     * ),
     * @OA\Response(response=404, description="Certificate not found"),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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
