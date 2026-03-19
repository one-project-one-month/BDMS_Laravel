<?php

namespace App\Console\Commands;

use App\Enums\BloodInventoryStatus;
use App\Models\BloodInventory;
use Illuminate\Console\Command;

class ExpireBloodInventories extends Command
{
    protected $signature = 'blood-inventories:expire';
    protected $description = 'Mark expired blood inventories as expired';

    public function handle(): int
    {
        $expiredCount = BloodInventory::query()
            ->where('status', BloodInventoryStatus::AVAILABLE->value)
            ->whereNotNull('expired_at')
            ->whereDate('expired_at', '<=', now()->toDateString())
            ->update(['status' => BloodInventoryStatus::EXPIRED->value]);

        $this->info("Expired {$expiredCount} blood inventories.");

        return self::SUCCESS;
    }
}
