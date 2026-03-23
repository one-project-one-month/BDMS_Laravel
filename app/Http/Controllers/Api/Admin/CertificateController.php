<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\CertificateRequest;
use App\Http\Resources\Api\Admin\CertificateResource;
use App\Models\Certificate;
use Storage;

class CertificateController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $certificates = Certificate::with('user')->paginate(config('pagnation.perPage'));

        return $this->successResponse(
            'Certificates retrieved successfully',
            $this->buildPaginatedResourceResponse(CertificateResource::class, $certificates),
            200
        );
    }

    /**
     * Store
     */
    public function store(CertificateRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('certificate_image')) {
                $file = $request->file('certificate_image');
                $path = $file->store('certificates', 'public');
                $data['certificate_image'] = $path;
            }

            $certificate = Certificate::create($data);

            return $this->successResponse(
                "Certificate Created Successfully!",
                new CertificateResource($certificate),
                201
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Certificate details
     */
    public function show($id)
    {
        $certificate = Certificate::with('user')->findOrFail($id);

        return $this->successResponse(
            "Certificate details fetched",
            new CertificateResource($certificate),
        );
    }

    /**
     * Certificate update
     */
    public function update(CertificateRequest $request, $id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $data = $request->validated();

            if ($request->hasFile('certificate_image')) {

                if ($certificate->certificate_image) {
                    Storage::disk('public')->delete($certificate->certificate_image);
                }

                $path = $request->file('certificate_image')->store('certificates', 'public');
                $data['certificate_image'] = $path;
            }

            $certificate->update($data);

            return $this->successResponse(
                "Certificate Updated Successfully!",
                new CertificateResource($certificate)
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Certificate delete (Soft Delete)
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);

        $certificate->delete();

        return $this->successResponse("Certificate deleted successfully", null, 204);
    }
}
