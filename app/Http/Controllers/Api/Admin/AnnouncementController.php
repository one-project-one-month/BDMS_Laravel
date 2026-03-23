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

    /**
     * @OA\Post(
     * path="/api/v1/announcements",
     * summary="Create a new announcement",
     * description="Store a new announcement in the database.",
     * tags={"Announcements"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"title","content"},
     * @OA\Property(property="title", type="string", example="Blood Donation Drive 2026"),
     * @OA\Property(property="content", type="string", example="We are organizing a blood donation camp at City Hospital."),
     * @OA\Property(property="expiredAt", type="string", format="date-time", example="2026-12-31T23:59:59Z"),
     * @OA\Property(property="isActive", type="boolean", example=true)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Announcement created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Announcement created successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Blood Donation Drive 2026"),
     * @OA\Property(property="content", type="string", example="..."),
     * @OA\Property(property="expiredAt", type="string", example="2026-12-31T23:59:59Z"),
     * @OA\Property(property="createdAt", type="string", example="2026-03-02T13:30:00Z")
     * )
     * )
     * ),
     * @OA\Response(response=400, description="Bad Request"),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Get(
     * path="/api/v1/announcements/{id}",
     * summary="Get a specific announcement",
     * description="Returns the details of a single announcement by its ID.",
     * tags={"Announcements"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the announcement to retrieve",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Announcement retrieved successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Blood Donation Drive 2026"),
     * @OA\Property(property="content", type="string", example="Join us to save lives!"),
     * @OA\Property(property="expiredAt", type="string", example="2026-12-31T23:59:59Z"),
     * @OA\Property(property="isActive", type="boolean", example=true),
     * @OA\Property(property="createdAt", type="string", example="2026-03-02T13:30:00Z")
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=404, description="Announcement not found")
     * )
     */
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

    /**
     * @OA\Put(
     * path="/api/v1/announcements/{id}",
     * summary="Update an existing announcement",
     * description="Update the title, content, or expiration date of an announcement.",
     * tags={"Announcements"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the announcement to update",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="title", type="string", example="Updated Announcement Title"),
     * @OA\Property(property="content", type="string", example="Updated content details..."),
     * @OA\Property(property="expiredAt", type="string", format="date-time", example="2026-12-31T23:59:59Z"),
     * @OA\Property(property="isActive", type="boolean", example=true)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Announcement updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Announcement updated successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Updated Announcement Title"),
     * @OA\Property(property="expiredAt", type="string", example="2026-12-31T23:59:59Z")
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=404, description="Announcement not found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Delete(
     * path="/api/v1/announcements/{id}",
     * summary="Delete an announcement",
     * description="Permanently remove an announcement from the system.",
     * tags={"Announcements"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the announcement to delete",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Announcement deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Announcement deleted successfully"),
     * @OA\Property(property="data", type="null", example=null)
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=404, description="Announcement not found")
     * )
     */
    public function destory($id)
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

    /**
     * @OA\Patch(
     * path="/api/v1/announcements/{id}/deactivate",
     * summary="Deactivate an announcement",
     * description="Change the announcement status to inactive, making it invisible to regular users.",
     * tags={"Announcements"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the announcement to deactivate",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Announcement deactivated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Announcement deactivated successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="isActive", type="boolean", example=false)
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=404, description="Announcement not found")
     * )
     */
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

    /**
     * @OA\Patch(
     * path="/api/v1/announcements/{id}/activate",
     * summary="Activate an announcement",
     * description="Change the announcement status to active. Note: An announcement cannot be activated if its expiration date has passed.",
     * tags={"Announcements"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the announcement to activate",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Announcement activated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Announcement activated successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="isActive", type="boolean", example=true)
     * )
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Bad Request - Cannot activate an expired announcement"
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=404, description="Announcement not found")
     * )
     */
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
