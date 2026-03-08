<?php

namespace App\Observers;

use App\Models\BloodRequest;
use App\Models\BloodInventory;
use App\Enums\BloodInventoryStatus;
use App\Enums\BloodRequestStatus;

class BloodRequestObserver
{
    /**
     * Handle the BloodRequest "created" event.
     */
    public function created(BloodRequest $bloodRequest): void
    {
        //
    }

    /**
     * Handle the BloodRequest "updated" event.
     */

    public function updated(BloodRequest $bloodRequest): void
    {
        if ($bloodRequest->status === BloodRequestStatus::FULFILLED->value) {

            BloodInventory::where('blood_request_id', $bloodRequest->id)
                ->update([
                    'status' => BloodInventoryStatus::USED->value
                ]);
        }
    }

    /**
     * Handle the BloodRequest "deleted" event.
     */
    public function deleted(BloodRequest $bloodRequest): void
    {
        //
    }

    /**
     * Handle the BloodRequest "restored" event.
     */
    public function restored(BloodRequest $bloodRequest): void
    {
        //
    }

    /**
     * Handle the BloodRequest "force deleted" event.
     */
    public function forceDeleted(BloodRequest $bloodRequest): void
    {
        //
    }
}
