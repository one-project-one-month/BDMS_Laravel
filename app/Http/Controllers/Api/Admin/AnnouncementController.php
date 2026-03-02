<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\AnnouncementRequest;
use App\Http\Resources\Api\Admin\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 * name="Announcements",
 * description="API Endpoints for managing announcements"
 * )
 */
class AnnouncementController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/announcements",
     * summary="Get list of all announcements",
     * description="Returns a collection of announcements. Can be filtered by is active.",
     * tags={"Announcements"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="isActive",
     * in="query",
     * description="Filter users by is active (e.g., true or false)",
     * required=false,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/AnnouncementResource")
     * ),
     * @OA\Property(property="message", type="string", example="Announcements retrieved successfully")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden"
     * )
     * )
     */
    public function index(Request $request)
    {
        try {
            $isActive = $request->query('is_active');

            $query = Announcement::query();

            if (!is_null($isActive)) {
                $query->where('is_active', $isActive);
            }

            $announcements = $query->paginate(config('pagination.perPage'));

            return $this->successResponse(
                'Announcements retrieved successfully',
                $this->buildPaginatedResourceResponse(
                    AnnouncementResource::class,
                    $announcements
                ),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(AnnouncementRequest $request)
    {
        try {
            $announcement = Announcement::create($request->validated());

            return $this->successResponse(
                'Announcement created successfully',
                new AnnouncementResource($announcement),
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        return $this->successResponse(
            'Announcement retrieved successfully',
            new AnnouncementResource($announcement),
            200
        );
    }

    public function update(AnnouncementRequest $request, $id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        $announcement->update($request->validated());

        return $this->successResponse(
            'Announcement updated successfully',
            new AnnouncementResource($announcement),
            200
        );
    }

    public function destroy($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        $announcement->delete();

        return $this->successResponse(
            'Announcement deleted successfully',
            null,
            204
        );
    }

    public function restore($id)
    {
        $announcement = Announcement::onlyTrashed()->find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found in trash', 404);
        }

        $announcement->restore();

        return $this->successResponse(
            'Announcement restored successfully',
            new AnnouncementResource($announcement),
            200
        );
    }

    public function forceDelete($id)
    {
        $announcement = Announcement::withTrashed()->find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        $announcement->forceDelete();

        return $this->successResponse(
            'Announcement permanently deleted',
            null,
            204
        );
    }

    public function trashedIndex()
    {
        $announcements = Announcement::onlyTrashed()
            ->paginate(config('pagination.perPage'));

        return $this->successResponse(
            'Trashed announcements retrieved successfully',
            $this->buildPaginatedResourceResponse(
                AnnouncementResource::class,
                $announcements
            ),
            200
        );
    }

    public function deactivate($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found.', 404);
        }

        if (!$announcement->is_active) {
            return $this->errorResponse('Announcement already inactive.', 400);
        }

        $announcement->update(['is_active' => false]);

        return $this->successResponse(
            'Announcement has been deactivated successfully',
            new AnnouncementResource($announcement),
            200
        );
    }
    public function activate($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found.', 404);
        }

        if ($announcement->is_active) {
            return $this->errorResponse('Announcement already active.', 400);
        }

        // Optional: prevent activating expired announcement
        if ($announcement->expired_at && $announcement->expired_at->isPast()) {
            return $this->errorResponse(
                'Cannot activate an expired announcement.',
                400
            );
        }

        $announcement->update(['is_active' => true]);

        return $this->successResponse(
            'Announcement has been activated successfully',
            new AnnouncementResource($announcement),
            200
        );
    }
}
