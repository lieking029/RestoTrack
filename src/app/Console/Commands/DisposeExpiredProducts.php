<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\WasteManagementService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DisposeExpiredProducts extends Command
{
    protected $signature = 'inventory:dispose-expired';

    protected $description = 'Automatically dispose expired products by moving them to waste';

    public function handle(WasteManagementService $wasteService): int
    {
        $expiredProducts = Product::where('remaining_stock', '>', 0)
            ->whereDate('expiration_date', '<', now())
            ->get();

        if ($expiredProducts->isEmpty()) {
            $this->info('No expired products to dispose.');
            Log::info('DisposeExpiredProducts: No expired products found.');
            return self::SUCCESS;
        }

        $count = 0;

        foreach ($expiredProducts as $product) {
            $wasteService->disposeExpired(
                $product,
                null,
                "Auto-disposed: expired on {$product->expiration_date->format('M d, Y')}"
            );

            $this->line("Disposed: {$product->name} ({$product->remaining_stock} units)");
            $count++;
        }

        $this->info("Successfully disposed {$count} expired product(s).");
        Log::info("DisposeExpiredProducts: Disposed {$count} expired product(s).", [
            'products' => $expiredProducts->pluck('name', 'id')->toArray(),
        ]);

        return self::SUCCESS;
    }
}
