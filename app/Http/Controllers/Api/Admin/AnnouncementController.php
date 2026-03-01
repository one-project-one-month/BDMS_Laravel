<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\Admin\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use ApiResponse;

    public function index()
{
    return response()->json([
        'test' => 'API works'
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'is_active'  => 'boolean',
            'expired_at' => 'nullable|date',
        ]);

        $announcement = Announcement::create($validated);

        return $this->successResponse(
            'Announcement created successfully',
            new AnnouncementResource($announcement),
            201
        );
    }

    public function show($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);

            return $this->successResponse(
                'Announcement details retrieved',
                new AnnouncementResource($announcement)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Announcement not found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);

            $validated = $request->validate([
                'title'      => 'sometimes|string|max:255',
                'content'    => 'sometimes|string',
                'is_active'  => 'boolean',
                'expired_at' => 'nullable|date',
            ]);

            $announcement->update($validated);

            return $this->successResponse(
                'Announcement updated successfully',
                new AnnouncementResource($announcement)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Update failed', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->delete();

            return $this->successResponse(
                'Announcement deleted successfully',
                null
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Delete failed', 500);
        }
    }

    public function restore($id)
    {
        try {
            $announcement = Announcement::onlyTrashed()->findOrFail($id);
            $announcement->restore();

            return $this->successResponse(
                'Announcement restored successfully',
                new AnnouncementResource($announcement)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Restore failed', 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $announcement = Announcement::withTrashed()->findOrFail($id);
            $announcement->forceDelete();

            return $this->successResponse(
                'Announcement permanently deleted',
                null,
                204
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Force delete failed', 500);
        }
    }

    public function deactivate($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->update(['is_active' => false]);

            return $this->successResponse(
                'Announcement deactivated',
                new AnnouncementResource($announcement)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Deactivation failed', 500);
        }
    }

    public function activate($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);

            if ($announcement->is_active) {
                return $this->errorResponse(
                    'Announcement already active',
                    400
                );
            }

            $announcement->update(['is_active' => true]);

            return $this->successResponse(
                'Announcement activated successfully',
                new AnnouncementResource($announcement)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Activation failed', 500);
        }
    }
}
